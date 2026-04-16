<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Profissional;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dashboard()
    {
        try {
            // Obter valores do tenant e location da sessão atual
            $tenantId = session('tenant_id', 1);
            $locationId = session('location_id', 1);

            // Log para debug
            \Log::info('Dashboard Reports - Tenant: ' . $tenantId . ', Location: ' . $locationId);

            // Buscar profissionais para o filtro com tratamento de erro
            $profissionais = collect(); // Inicializar como coleção vazia

            try {
                $profissionais = Profissional::with('especialidade')
                    ->where('location_id', $locationId)
                    ->where('ativo', true)
                    ->orderBy('nome')
                    ->get();

                \Log::info('Profissionais encontrados: ' . $profissionais->count());
            } catch (\Exception $e) {
                \Log::error('Erro ao buscar profissionais: ' . $e->getMessage());
                $profissionais = collect(); // Manter coleção vazia em caso de erro
            }

            // Estatísticas rápidas do dia com tratamento de erro
            $quickStats = [
                'consultas_hoje' => 0,
                'atendimentos' => 0,
                'aguardando' => 0,
                'profissionais_ativos' => 0
            ];

            try {
                $hoje = now()->format('Y-m-d');

                $quickStats['consultas_hoje'] = Consulta::where('tenant_id', $tenantId)
                    ->where('location_id', $locationId)
                    ->whereDate('agendado_em', $hoje)
                    ->count();

                $quickStats['atendimentos'] = Consulta::where('tenant_id', $tenantId)
                    ->where('location_id', $locationId)
                    ->whereDate('agendado_em', $hoje)
                    ->where('status', Consulta::STATUS_ATENDIDO)
                    ->count();

                $quickStats['aguardando'] = Consulta::where('tenant_id', $tenantId)
                    ->where('location_id', $locationId)
                    ->whereDate('agendado_em', $hoje)
                    ->where('status', Consulta::STATUS_AGUARDANDO)
                    ->count();

                $quickStats['profissionais_ativos'] = Profissional::where('location_id', $locationId)
                    ->where('ativo', true)
                    ->count();

                \Log::info('Quick Stats calculadas: ', $quickStats);
            } catch (\Exception $e) {
                \Log::error('Erro ao calcular estatísticas rápidas: ' . $e->getMessage());
            }

            return view('reports.dashboard', compact('profissionais', 'quickStats'));
        } catch (\Exception $e) {
            \Log::error('Erro no dashboard de relatórios: ' . $e->getMessage());

            // Em caso de erro, redirecionar com mensagem
            return redirect()->back()->with('error', 'Erro ao carregar dashboard de relatórios: ' . $e->getMessage());
        }
    }

    public function attendance(Request $request)
    {
        // Obter valores do tenant e location da sessão atual
        $tenantId = session('tenant_id') ?? 1;
        $locationId = session('location_id') ?? 1;

        // Obter data da query string ou usar data atual
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $startDate = $request->get('start_date', $selectedDate);
        $endDate = $request->get('end_date', $selectedDate);
        $selectedProfessional = $request->get('professional_id', null);

        $date = Carbon::parse($selectedDate);
        $dateStart = Carbon::parse($startDate);
        $dateEnd = Carbon::parse($endDate);

        // Estatísticas gerais do período (pode ser um dia ou intervalo)
        $baseQuery = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId);

        // Aplicar filtro de data (período ou data única)
        if ($startDate === $endDate) {
            $baseQuery->whereDate('agendado_em', $date);
        } else {
            $baseQuery->whereBetween('agendado_em', [$dateStart->startOfDay(), $dateEnd->endOfDay()]);
        }

        // Aplicar filtro de profissional se selecionado
        if ($selectedProfessional) {
            $baseQuery->where('profissional_id', $selectedProfessional);
        }

        $totalConsultas = (clone $baseQuery)->count();

        $attendedCount = (clone $baseQuery)
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->count();

        $cancelledCount = (clone $baseQuery)
            ->where('status', Consulta::STATUS_CANCELADO)
            ->count();

        $returnsCount = (clone $baseQuery)
            ->where('tipo', Consulta::TIPO_RETORNO)
            ->count();

        // Encaminhamentos - verificar se tem relacionamento com tabela encaminhamento
        $referralsCount = (clone $baseQuery)
            ->whereHas('encaminhamento')
            ->count();

        // Prioritários - incluir tanto prioridade alta quanto emergência
        $priorityCount = (clone $baseQuery)
            ->whereIn('prioridade', [Consulta::PRIORIDADE, Consulta::PRIORIDADE_EMERGENCIA])
            ->count();

        // Para primeira consulta - verificar se é a primeira consulta do paciente no sistema
        $dateCondition = $startDate === $endDate
            ? "DATE(c1.agendado_em) = ?"
            : "c1.agendado_em BETWEEN ? AND ?";

        $dateParams = $startDate === $endDate
            ? [$tenantId, $locationId, $date->format('Y-m-d'), Consulta::STATUS_CANCELADO]
            : [$tenantId, $locationId, $dateStart->format('Y-m-d H:i:s'), $dateEnd->format('Y-m-d H:i:s'), Consulta::STATUS_CANCELADO];

        $professionalCondition = $selectedProfessional ? "AND c1.profissional_id = ?" : "";
        if ($selectedProfessional) {
            $dateParams[] = $selectedProfessional;
        }

        $firstTimeCount = DB::select("
            SELECT COUNT(*) as count FROM consulta c1
            WHERE c1.tenant_id = ?
            AND c1.location_id = ?
            AND $dateCondition
            AND c1.status != ?
            $professionalCondition
            AND NOT EXISTS (
                SELECT 1 FROM consulta c2
                WHERE c2.pessoa_paciente_id = c1.pessoa_paciente_id
                AND c2.tenant_id = c1.tenant_id
                AND c2.location_id = c1.location_id
                AND c2.agendado_em < c1.agendado_em
                AND c2.deleted_at IS NULL
            )
            AND c1.deleted_at IS NULL
        ", $dateParams)[0]->count ?? 0;
        // Cálculo de tempos médios
        $avgWaitTime = $this->calculateAverageWaitTime($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional);
        $avgServiceTime = $this->calculateAverageServiceTime($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional);

        $stats = [
            'scheduled' => $totalConsultas,
            'attended' => $attendedCount,
            'cancelled' => $cancelledCount,
            'returns' => $returnsCount,
            'referrals' => $referralsCount,
            'avg_wait_time' => $avgWaitTime . ' min',
            'avg_service_time' => $avgServiceTime . ' min',
            'priority_patients' => $priorityCount,
            'first_time' => $firstTimeCount
        ];

        // Estatísticas por profissional
        $professionalStats = $this->getProfessionalStatistics($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional);

        // Buscar profissionais para o filtro
        $profissionais = Profissional::with('especialidade')
            ->where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('reports.attendance', compact('stats', 'professionalStats', 'selectedDate', 'startDate', 'endDate', 'selectedProfessional', 'profissionais'));
    }

    private function getProfessionalStatistics($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional = null)
    {
        $profissionais = Profissional::with('especialidade')
            ->where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome', 'desc')
            ->when($selectedProfessional, function ($query, $professionalId) {
                return $query->where('id', $professionalId);
            })
            ->get();

        return $profissionais->map(function ($profissional) use ($dateStart, $dateEnd, $tenantId, $locationId) {
            $consultasQuery = Consulta::where('tenant_id', $tenantId)
                ->where('location_id', $locationId)
                ->where('profissional_id', $profissional->id);

            // Aplicar filtro de período
            if ($dateStart->format('Y-m-d') === $dateEnd->format('Y-m-d')) {
                $consultasQuery->whereDate('agendado_em', $dateStart);
            } else {
                $consultasQuery->whereBetween('agendado_em', [$dateStart->startOfDay(), $dateEnd->endOfDay()]);
            }

            $scheduledCount = (clone $consultasQuery)->count();

            $attendedCount = (clone $consultasQuery)
                ->where('status', Consulta::STATUS_ATENDIDO)
                ->count();

            $returnsCount = (clone $consultasQuery)
                ->where('tipo', Consulta::TIPO_RETORNO)
                ->count();

            $referralsCount = (clone $consultasQuery)
                ->whereHas('encaminhamento')
                ->count();

            $totalCount = (clone $consultasQuery)->count();

            return [
                'name' => $profissional->nome,
                'specialty' => $profissional->especialidade->descricao ?? 'Não informada',
                'scheduled' => $scheduledCount,
                'attended' => $attendedCount,
                'returns' => $returnsCount,
                'referrals' => $referralsCount,
                'total' => $totalCount
            ];
        })->toArray();
    }

    private function calculateAverageWaitTime($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional = null)
    {
        $consultas = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereNotNull('atendido_em')
            ->whereNotNull('chegada_em')
            ->when($selectedProfessional, function ($query, $professionalId) {
                return $query->where('profissional_id', $professionalId);
            });

        // Aplicar filtro de período
        if ($dateStart->format('Y-m-d') === $dateEnd->format('Y-m-d')) {
            $consultas->whereDate('agendado_em', $dateStart);
        } else {
            $consultas->whereBetween('agendado_em', [$dateStart->startOfDay(), $dateEnd->endOfDay()]);
        }

        $consultas = $consultas->get();

        if ($consultas->isEmpty()) {
            return 0;
        }

        $totalMinutes = $consultas->sum(function ($consulta) {
            if ($consulta->chegada_em && $consulta->atendido_em) {
                return $consulta->chegada_em->diffInMinutes($consulta->atendido_em);
            }
            return 0;
        });

        $validConsultas = $consultas->filter(function ($consulta) {
            return $consulta->chegada_em && $consulta->atendido_em;
        });

        return $validConsultas->count() > 0 ? round($totalMinutes / $validConsultas->count()) : 0;
    }

    private function calculateAverageServiceTime($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional = null)
    {
        $consultas = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->whereNotNull('atendido_em')
            ->whereNotNull('chegada_em')
            ->when($selectedProfessional, function ($query, $professionalId) {
                return $query->where('profissional_id', $professionalId);
            });

        // Aplicar filtro de período
        if ($dateStart->format('Y-m-d') === $dateEnd->format('Y-m-d')) {
            $consultas->whereDate('agendado_em', $dateStart);
        } else {
            $consultas->whereBetween('agendado_em', [$dateStart->startOfDay(), $dateEnd->endOfDay()]);
        }

        $consultas = $consultas->get();

        if ($consultas->isEmpty()) {
            return 0;
        }

        // Calcular o tempo de atendimento: da chegada até ser atendido + tempo estimado de consulta (30min padrão)
        $totalMinutes = $consultas->sum(function ($consulta) {
            if ($consulta->atendido_em && $consulta->chegada_em) {
                // Tempo de espera + tempo de consulta estimado
                $waitTime = $consulta->chegada_em->diffInMinutes($consulta->atendido_em);
                return $waitTime + 30; // 30 minutos é o tempo médio de uma consulta
            }
            return 30; // Se não tem chegada_em, considera apenas o tempo de consulta
        });

        return round($totalMinutes / $consultas->count());
    }

    public function exportAttendance(Request $request)
    {
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($selectedDate);

        // TODO: Implement Excel export with date filter
        $filename = 'attendance_' . $date->format('Y-m-d') . '.xlsx';

        return response()->download(
            storage_path('app/reports/' . $filename)
        );
    }

    public function attendancePdf(Request $request)
    {
        // Obter valores do tenant e location da sessão atual
        $tenantId = session('tenant_id') ?? 1;
        $locationId = session('location_id') ?? 1;

        // Obter data da query string ou usar data atual
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $startDate = $request->get('start_date', $selectedDate);
        $endDate = $request->get('end_date', $selectedDate);
        $selectedProfessional = $request->get('professional_id', null);

        $date = \Carbon\Carbon::parse($selectedDate);
        $dateStart = \Carbon\Carbon::parse($startDate);
        $dateEnd = \Carbon\Carbon::parse($endDate);

        // Estatísticas gerais do período (usando mesmo código do método attendance)
        $baseQuery = \App\Models\Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId);

        // Aplicar filtro de data (período ou data única)
        if ($startDate === $endDate) {
            $baseQuery->whereDate('agendado_em', $date);
        } else {
            $baseQuery->whereBetween('agendado_em', [$dateStart->startOfDay(), $dateEnd->endOfDay()]);
        }

        // Aplicar filtro de profissional se selecionado
        if ($selectedProfessional) {
            $baseQuery->where('profissional_id', $selectedProfessional);
        }

        $totalConsultas = (clone $baseQuery)->count();

        $attendedCount = (clone $baseQuery)
            ->where('status', \App\Models\Consulta::STATUS_ATENDIDO)
            ->count();

        $cancelledCount = (clone $baseQuery)
            ->where('status', \App\Models\Consulta::STATUS_CANCELADO)
            ->count();

        $returnsCount = (clone $baseQuery)
            ->where('tipo', \App\Models\Consulta::TIPO_RETORNO)
            ->count();

        $referralsCount = (clone $baseQuery)
            ->whereHas('encaminhamento')
            ->count();

        $priorityCount = (clone $baseQuery)
            ->whereIn('prioridade', [\App\Models\Consulta::PRIORIDADE, \App\Models\Consulta::PRIORIDADE_EMERGENCIA])
            ->count();

        // Cálculo de tempos médios
        $avgWaitTime = $this->calculateAverageWaitTime($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional);
        $avgServiceTime = $this->calculateAverageServiceTime($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional);

        $stats = [
            'scheduled' => $totalConsultas,
            'attended' => $attendedCount,
            'cancelled' => $cancelledCount,
            'returns' => $returnsCount,
            'referrals' => $referralsCount,
            'avg_wait_time' => $avgWaitTime . ' min',
            'avg_service_time' => $avgServiceTime . ' min',
            'priority_patients' => $priorityCount,
            'first_time' => 0 // Por simplicidade, mantendo 0 por enquanto
        ];

        // Estatísticas por profissional
        $professionalStats = $this->getProfessionalStatistics($dateStart, $dateEnd, $tenantId, $locationId, $selectedProfessional);

        // Buscar profissionais para o filtro
        $profissionais = \App\Models\Profissional::with('especialidade')
            ->where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $selectedProfessionalData = null;
        if ($selectedProfessional) {
            $selectedProfessionalData = $profissionais->where('id', $selectedProfessional)->first();
        }

        // Gerar PDF usando DomPDF
        $pdf = \PDF::loadView('reports.pdf', compact(
            'stats',
            'professionalStats',
            'selectedDate',
            'startDate',
            'endDate',
            'selectedProfessional',
            'selectedProfessionalData',
            'profissionais'
        ));

        $filename = 'relatorio_atendimentos_' . $date->format('Y-m-d') . '.pdf';

        return $pdf->stream($filename);
    }
}

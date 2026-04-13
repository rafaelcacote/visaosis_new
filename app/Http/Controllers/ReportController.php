<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Profissional;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        // Obter valores do tenant e location da sessão atual
        $tenantId = session('tenant_id') ?? 1;
        $locationId = session('location_id') ?? 1;

        // Obter data da query string ou usar data atual
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        // Estatísticas gerais do dia
        $totalConsultas = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->count();

        $attendedCount = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->count();

        $cancelledCount = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->where('status', Consulta::STATUS_CANCELADO)
            ->count();

        $returnsCount = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->where('tipo', Consulta::TIPO_RETORNO)
            ->count();

        // Encaminhamentos - verificar se tem relacionamento com tabela encaminhamento
        $referralsCount = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->whereHas('encaminhamento')
            ->count();

        // Prioritários - incluir tanto prioridade alta quanto emergência
        $priorityCount = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->whereIn('prioridade', [Consulta::PRIORIDADE, Consulta::PRIORIDADE_EMERGENCIA])
            ->count();

        // Para primeira consulta - verificar se é a primeira consulta do paciente no sistema
        $firstTimeCount = DB::select("
            SELECT COUNT(*) as count FROM consulta c1
            WHERE c1.tenant_id = ?
            AND c1.location_id = ?
            AND DATE(c1.agendado_em) = ?
            AND c1.status != ?
            AND NOT EXISTS (
                SELECT 1 FROM consulta c2
                WHERE c2.pessoa_paciente_id = c1.pessoa_paciente_id
                AND c2.tenant_id = c1.tenant_id
                AND c2.location_id = c1.location_id
                AND c2.agendado_em < c1.agendado_em
                AND c2.deleted_at IS NULL
            )
            AND c1.deleted_at IS NULL
        ", [$tenantId, $locationId, $date->format('Y-m-d'), Consulta::STATUS_CANCELADO])[0]->count ?? 0;

        // Cálculo de tempos médios
        $avgWaitTime = $this->calculateAverageWaitTime($date, $tenantId, $locationId);
        $avgServiceTime = $this->calculateAverageServiceTime($date, $tenantId, $locationId);

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
        $professionalStats = $this->getProfessionalStatistics($date, $tenantId, $locationId);

        return view('reports.attendance', compact('stats', 'professionalStats', 'selectedDate'));
    }

    private function getProfessionalStatistics($date, $tenantId, $locationId)
    {
        $profissionais = Profissional::with('especialidade')
            ->where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome', 'desc')
            ->get();

        return $profissionais->map(function ($profissional) use ($date, $tenantId, $locationId) {
            $consultasQuery = Consulta::where('tenant_id', $tenantId)
                ->where('location_id', $locationId)
                ->where('profissional_id', $profissional->id)
                ->whereDate('agendado_em', $date);

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

    private function calculateAverageWaitTime($date, $tenantId, $locationId)
    {
        $consultas = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->whereNotNull('atendido_em')
            ->whereNotNull('chegada_em')
            ->get();

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

    private function calculateAverageServiceTime($date, $tenantId, $locationId)
    {
        $consultas = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->whereNotNull('atendido_em')
            ->whereNotNull('chegada_em')
            ->get();

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
}

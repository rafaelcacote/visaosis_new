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

        // Para primeira consulta, vamos considerar o tipo CONSULTA (não retorno)
        $firstTimeCount = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $date)
            ->where('tipo', Consulta::TIPO_CONSULTA)
            ->count();

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
            ->get();

        if ($consultas->isEmpty()) {
            return 0;
        }

        // Calcular o tempo baseado na diferença entre agendado_em e atendido_em
        $totalMinutes = $consultas->sum(function ($consulta) {
            if ($consulta->atendido_em && $consulta->agendado_em) {
                return $consulta->agendado_em->diffInMinutes($consulta->atendido_em);
            }
            return 0;
        });

        $validConsultas = $consultas->filter(function ($consulta) {
            return $consulta->atendido_em && $consulta->agendado_em;
        });

        return $validConsultas->count() > 0 ? round($totalMinutes / $validConsultas->count()) : 0;
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

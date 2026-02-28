<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Pessoa;
use App\Models\Profissional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        // Obter valores do tenant e location da sessão atual
        $tenantId = session('tenant_id') ?? 1;
        $locationId = session('location_id') ?? 1;

        // Buscar consultas do dia atual com estatísticas filtradas por tenant e location
        $today = now()->format('Y-m-d');

        $totalToday = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $today)
            ->count();
        $waiting = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $today)
            ->where('status', Consulta::STATUS_AGUARDANDO)
            ->count();
        $inService = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $today)
            ->where('status', Consulta::STATUS_EM_ATENDIMENTO)
            ->count();
        $completed = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $today)
            ->where('status', Consulta::STATUS_ATENDIDO)
            ->count();
        $cancelled = Consulta::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $today)
            ->where('status', Consulta::STATUS_CANCELADO)
            ->count();

        $stats = [
            'total_today' => $totalToday,
            'waiting' => $waiting,
            'in_service' => $inService,
            'completed' => $completed,
            'cancelled' => $cancelled
        ];

        // Buscar consultas do dia com relacionamentos filtradas por tenant e location
        $consultas = Consulta::with(['paciente', 'profissional.especialidade'])
            ->where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->whereDate('agendado_em', $today)
            ->orderBy('agendado_em', 'desc')
            ->get();

        // Transformar consultas em formato de pacientes para a view
        $patients = $consultas->map(function ($consulta) {
            return [
                'id' => $consulta->id,
                'name' => $consulta->paciente->nome ?? 'N/A',
                'arrival_time' => $consulta->chegada_em ?? $consulta->agendado_em,
                'type' => $this->mapTipo($consulta->tipo),
                'status' => $this->mapStatus($consulta->status),
                'priority' => $this->mapPrioridade($consulta->prioridade),
                'professional' => $consulta->profissional->nome ?? 'N/A'
            ];
        })->toArray();

        // Buscar profissionais ativos do mesmo tenant e location
        $profissionais = Profissional::with('especialidade')
            ->where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        // Transformar profissionais em formato para a view
        $professionals = $profissionais->map(function ($profissional) use ($consultas) {
            $consultasHoje = $consultas->where('profissional_id', $profissional->id);
            $emAtendimento = $consultasHoje->where('status', Consulta::STATUS_EM_ATENDIMENTO)->first();
            
            return [
                'id' => $profissional->id,
                'name' => $profissional->nome,
                'specialty' => $profissional->especialidade->descricao ?? 'Especialidade não informada',
                'status' => $emAtendimento ? 'busy' : 'available',
                'current_patient' => $emAtendimento ? ($emAtendimento->paciente->nome ?? 'N/A') : null,
                'patients_today' => $consultasHoje->count()
            ];
        })->toArray();

        return view('attendance.index', compact('stats', 'patients', 'professionals'));
    }

    public function triage()
    {
        // Obter valores do tenant e location da sessão atual
        $tenantId = session('tenant_id') ?? 1;
        $locationId = session('location_id') ?? 1;

        $profissionais = Profissional::where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('attendance.triage', compact('profissionais'));
    }

    public function storeTriage(Request $request)
    {
        // Reutilizar a lógica do RecepcaoController
        $recepcaoController = new RecepcaoController();
        $response = $recepcaoController->storeTriage($request);
        
        // Se for redirecionamento, redirecionar para attendance.index
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return redirect()->route('attendance.index')->with($response->getSession()->all());
        }
        
        return $response;
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $consulta = Consulta::findOrFail($id);
        
        // Mapear status de string para constante numérica
        $statusMap = [
            'waiting' => Consulta::STATUS_AGUARDANDO,
            'in_service' => Consulta::STATUS_EM_ATENDIMENTO,
            'completed' => Consulta::STATUS_ATENDIDO,
            'cancelled' => Consulta::STATUS_CANCELADO,
        ];

        $statusValue = $statusMap[$request->status] ?? Consulta::STATUS_AGUARDANDO;
        
        $consulta->status = $statusValue;

        // Atualizar timestamp baseado no status
        if ($request->status == 'in_service') {
            $consulta->atendido_em = now();
        } elseif ($request->status == 'completed') {
            if (!$consulta->atendido_em) {
                $consulta->atendido_em = now();
            }
        }

        $consulta->save();

        return response()->json([
            'success' => true,
            'message' => 'Status da consulta atualizado com sucesso!',
            'consulta' => $consulta
        ]);
    }

    public function searchPatient(Request $request)
    {
        // Reutilizar a lógica do RecepcaoController
        $recepcaoController = new RecepcaoController();
        return $recepcaoController->searchPatient($request);
    }

    private function mapTipo($tipo)
    {
        $map = [
            Consulta::TIPO_CONSULTA => 'consulta',
            Consulta::TIPO_RETORNO => 'retorno',
            Consulta::TIPO_CONFERENCIA => 'conferencia',
        ];
        return $map[$tipo] ?? 'consulta';
    }

    private function mapStatus($status)
    {
        $map = [
            Consulta::STATUS_AGUARDANDO => 'waiting',
            Consulta::STATUS_EM_ATENDIMENTO => 'in_service',
            Consulta::STATUS_ATENDIDO => 'completed',
            Consulta::STATUS_CANCELADO => 'cancelled',
        ];
        return $map[$status] ?? 'waiting';
    }

    private function mapPrioridade($prioridade)
    {
        $map = [
            Consulta::PRIORIDADE_NORMAL => 'normal',
            Consulta::PRIORIDADE => 'normal',
            Consulta::PRIORIDADE_EMERGENCIA => 'urgent',
        ];
        return $map[$prioridade] ?? 'normal';
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Pessoa;
use App\Models\Profissional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecepcaoController extends Controller
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

        // Buscar profissionais ativos do mesmo tenant e location
        $profissionais = Profissional::with('especialidade')
            ->where('location_id', $locationId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('recepcao.index', compact('stats', 'consultas', 'profissionais'));
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

        return view('recepcao.triage', compact('profissionais'));
    }

    public function searchPatient(Request $request)
    {
        $term = $request->get('term');

        // Obter tenant_id e location_id da sessão (sem valores padrão)
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        // Log dos valores da sessão para debug
        \Log::info('Busca de paciente iniciada', [
            'term' => $term,
            'tenant_id_session' => $tenantId,
            'location_id_session' => $locationId,
            'user_id' => Auth::id(),
            'user_locations_count' => count($userLocations),
            'user_locations' => $userLocations
        ]);

        // Validar se temos os valores necessários
        if (!$tenantId || !$locationId) {
            \Log::warning('Tenant ou Location não encontrados na sessão', [
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'found' => false,
                'error' => 'Sessão inválida. Por favor, faça login novamente.'
            ], 400);
        }

        // Verificar se a location_id da sessão está nas locations do usuário
        $validLocation = collect($userLocations)->firstWhere('location_id', $locationId);
        if (!$validLocation) {
            \Log::warning('Location da sessão não está nas locations do usuário', [
                'location_id_session' => $locationId,
                'user_locations' => $userLocations,
                'user_id' => Auth::id()
            ]);

            // Tentar pegar a primeira location válida do usuário
            if (!empty($userLocations)) {
                $firstLocation = $userLocations[0];
                $locationId = $firstLocation['location_id'] ?? null;
                $tenantId = $firstLocation['tenant_id'] ?? null;

                // Atualizar sessão com a location correta
                session([
                    'location_id' => $locationId,
                    'tenant_id' => $tenantId,
                    'location' => (object)($firstLocation['location'] ?? []),
                    'tenant' => (object)($firstLocation['tenant'] ?? [])
                ]);

                \Log::info('Location corrigida automaticamente', [
                    'new_location_id' => $locationId,
                    'new_tenant_id' => $tenantId
                ]);
            } else {
                return response()->json([
                    'found' => false,
                    'error' => 'Nenhuma location disponível. Por favor, selecione uma location.'
                ], 400);
            }
        }

        if (empty($term)) {
            return response()->json(['found' => false]);
        }

        // Limpar o termo de busca
        $term = trim($term);
        $termLimpoNumerico = preg_replace('/[^0-9]/', '', $term);

        // Detectar o driver do banco de dados
        $driver = DB::connection()->getDriverName();
        $isPostgres = ($driver === 'pgsql');

        // Construir a query de busca
        $query = Pessoa::where('tenant_id', $tenantId)
            ->where('location_id', $locationId)
            ->where(function ($q) use ($term, $termLimpoNumerico, $isPostgres) {
                // Buscar por nome (case insensitive)
                if ($isPostgres) {
                    // PostgreSQL: usar ILIKE
                    $q->where('nome', 'ILIKE', "%{$term}%");
                } else {
                    // MySQL/SQLite: usar LOWER com LIKE
                    $q->whereRaw('LOWER(nome) LIKE ?', ['%' . mb_strtolower($term, 'UTF-8') . '%']);
                }

                // Buscar por CPF se o termo contém números
                if (!empty($termLimpoNumerico) && strlen($termLimpoNumerico) >= 3) {
                    $q->orWhere('cpf', 'LIKE', "%{$termLimpoNumerico}%");
                }

                // Buscar por telefone se o termo contém números
                if (!empty($termLimpoNumerico) && strlen($termLimpoNumerico) >= 3) {
                    $q->orWhere('telefone', 'LIKE', "%{$termLimpoNumerico}%");
                }
            });

        $pacientes = $query->orderBy('nome')->limit(10)->get();

        \Log::info('Resultado da busca', [
            'count' => $pacientes->count(),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        if ($pacientes->count() > 0) {
            return response()->json([
                'found' => true,
                'count' => $pacientes->count(),
                'multiple' => $pacientes->count() > 1,
                'pacientes' => $pacientes->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nome' => $p->nome,
                        'cpf' => $p->cpf,
                        'cpf_formatado' => $p->cpf_formatado,
                        'telefone' => $p->telefone,
                        'telefone_formatado' => $p->telefone_formatado,
                        'email' => $p->email,
                        'nascimento_em' => $p->nascimento_em ? $p->nascimento_em->format('Y-m-d') : null,
                        'idade' => $p->idade
                    ];
                }),
                // Para compatibilidade com código existente, manter 'paciente' quando há apenas um resultado
                'paciente' => $pacientes->count() === 1 ? [
                    'id' => $pacientes->first()->id,
                    'nome' => $pacientes->first()->nome,
                    'cpf' => $pacientes->first()->cpf,
                    'cpf_formatado' => $pacientes->first()->cpf_formatado,
                    'telefone' => $pacientes->first()->telefone,
                    'telefone_formatado' => $pacientes->first()->telefone_formatado,
                    'email' => $pacientes->first()->email,
                    'nascimento_em' => $pacientes->first()->nascimento_em ? $pacientes->first()->nascimento_em->format('Y-m-d') : null,
                    'idade' => $pacientes->first()->idade
                ] : null
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function storeTriage(Request $request)
    {
        try {
            // Validação simples
            if (!$request->nome || !$request->cpf) {
                return redirect()->back()
                    ->with('validation_message', 'Nome e CPF são obrigatórios')
                    ->withInput();
            }

            // Obter valores do tenant atual
            $tenantId = session('tenant_id') ?? 1;
            $locationId = session('location_id') ?? 1;
            $userId = Auth::id() ?? 1;

            // Limpar CPF para busca (remover formatação)
            $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);

            // Validar CPF básico (evitar CPFs óbvios como 111.111.111-11)
            if (strlen($cpfLimpo) === 11) {
                $cpfsInvalidos = [
                    '00000000000',
                    '11111111111',
                    '22222222222',
                    '33333333333',
                    '44444444444',
                    '55555555555',
                    '66666666666',
                    '77777777777',
                    '88888888888',
                    '99999999999'
                ];
                if (in_array($cpfLimpo, $cpfsInvalidos)) {
                    return redirect()->back()
                        ->with('validation_message', 'CPF informado não é válido.')
                        ->withInput();
                }
            }

            // Buscar pessoa existente pelo CPF, tenant_id e location_id
            $pessoa = Pessoa::where('cpf', $cpfLimpo)
                ->where('tenant_id', $tenantId)
                ->where('location_id', $locationId)
                ->first();

            // Se pessoa existe, verificar se os dados conferem
            if ($pessoa) {
                // Verificar se o nome é muito diferente (possível duplicidade)
                $nomeExistente = strtoupper(trim($pessoa->nome));
                $nomeInformado = strtoupper(trim($request->nome));

                // Se os nomes são muito diferentes, pode ser tentativa de duplicar CPF
                if ($nomeExistente !== $nomeInformado) {
                    // Verificar similaridade dos nomes
                    similar_text($nomeExistente, $nomeInformado, $similaridade);

                    if ($similaridade < 70) { // Menos de 70% de similaridade
                        return redirect()->back()
                            ->with('validation_warning', "CPF {$request->cpf} já está cadastrado para: {$pessoa->nome}. Verifique se não há duplicidade ou erro de digitação.")
                            ->withInput();
                    }
                }

                // Se chegou aqui, é a mesma pessoa ou nomes similares - usar pessoa existente
            } else {
                // Pessoa não existe, criar nova
                $pessoa = new Pessoa();
                $pessoa->tenant_id = $tenantId;
                $pessoa->location_id = $locationId;
                $pessoa->user_id = $userId;
                $pessoa->cpf = $cpfLimpo;
                $pessoa->nome = $request->nome;
                $pessoa->nascimento_em = $request->nascimento_em;
                $pessoa->telefone = $request->telefone;
                $pessoa->email = $request->email;
                $pessoa->ativo = true;
                $pessoa->save();

                // Verificar se foi salva corretamente
                if (!$pessoa->id) {
                    return redirect()->back()
                        ->with('validation_message', 'Erro ao criar novo paciente')
                        ->withInput();
                }
            }

            // Criar consulta com os mesmos tenant values
            $consulta = new Consulta();
            $consulta->tenant_id = $tenantId;
            $consulta->location_id = $locationId;
            $consulta->user_id = $userId;
            $consulta->pessoa_paciente_id = $pessoa->id;
            $consulta->profissional_id = $request->profissional_id;
            $consulta->agendado_em = now();
            $consulta->tipo = $request->tipo ?? Consulta::TIPO_CONSULTA;
            $consulta->prioridade = $request->prioridade ?? Consulta::PRIORIDADE_NORMAL;
            $consulta->status = Consulta::STATUS_AGUARDANDO;
            $consulta->observacoes = $request->observacoes;
            $consulta->chegada_em = now();
            $consulta->ativo = true;
            $consulta->save();

            return redirect()->route('recepcao.index')->with('success', 'Triagem salva com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro interno do sistema: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'pessoa_paciente_id' => 'required|exists:pessoas,id',
            'profissional_id' => 'required|exists:profissionais,id',
            'tipo' => 'required|integer',
            'prioridade' => 'required|integer'
        ]);

        $tenantId = session('tenant_id') ?? 1;
        $locationId = session('location_id') ?? 1;
        $userId = Auth::id() ?? 1;

        $consulta = Consulta::create([
            'tenant_id' => $tenantId,
            'location_id' => $locationId,
            'user_id' => $userId,
            'pessoa_paciente_id' => $request->pessoa_paciente_id,
            'profissional_id' => $request->profissional_id,
            'agendado_em' => now(),
            'tipo' => $request->tipo,
            'prioridade' => $request->prioridade,
            'status' => Consulta::STATUS_AGUARDANDO,
            'observacoes' => $request->observacoes,
            'chegada_em' => now(),
            'ativo' => true
        ]);

        return redirect()->route('recepcao.index')->with('success', 'Paciente adicionado à fila de atendimento!');
    }

    public function updateStatus(Request $request, $consulta)
    {
        $request->validate([
            'status' => 'required|integer'
        ]);

        $consulta = Consulta::findOrFail($consulta);
        $consulta->status = $request->status;

        // Atualizar timestamp baseado no status
        if ($request->status == Consulta::STATUS_EM_ATENDIMENTO) {
            $consulta->atendido_em = now();
        } elseif ($request->status == Consulta::STATUS_ATENDIDO) {
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

    public function show(Consulta $consulta)
    {
        // Carregar relacionamentos
        $consulta->load(['paciente', 'profissional.especialidade']);

        return view('recepcao.show', compact('consulta'));
    }

    public function dashboard()
    {
        return $this->index();
    }
}

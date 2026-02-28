<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profissional;
use App\Models\Especialidade;
use App\Rules\ValidCpf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class ProfissionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
            'especialidade_id' => $request->get('especialidade_id', ''),
            'status' => $request->get('status', ''),
        ];

        // Obter tenant_id e location_ids da sessão
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        // Buscar location_ids do tenant atual
        $locationIds = [];
        if ($tenantId) {
            // Filtrar locations do tenant atual
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            // Se não tem tenant_id mas tem location_id, usar apenas essa location
            $locationIds = [$locationId];
        }

        // Se não tem location_ids válidos, retornar vazio
        if (empty($locationIds)) {
            $profissionais = Profissional::whereRaw('1 = 0')->paginate(15);
            $especialidades = Especialidade::ativos()->orderBy('descricao')->get();
            return view('profissionais.index', compact('profissionais', 'filters', 'especialidades'));
        }

        $especialidades = Especialidade::ativos()->orderBy('descricao')->get();

        $query = Profissional::with('especialidade')
            ->whereIn('location_id', $locationIds);

        // Filtro de busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $cpfLimpo = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $cpfLimpo) {
                $q->where('nome', 'ILIKE', "%{$search}%")
                    ->orWhere('registro_conselho', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");

                // Se a busca parece ser um CPF (só números e tem pelo menos 3 dígitos)
                if ($cpfLimpo && strlen($cpfLimpo) >= 3) {
                    $q->orWhere('cpf', 'LIKE', "%{$cpfLimpo}%");
                }
            });
        }

        // Filtro por especialidade
        if (!empty($filters['especialidade_id'])) {
            $query->where('especialidade_id', $filters['especialidade_id']);
        }

        // Filtro de status
        if ($filters['status'] !== '') {
            $query->where('ativo', $filters['status'] == '1');
        }

        // Paginação
        $profissionais = $query->orderBy('nome')
            ->paginate(15);

        return view('profissionais.index', compact('profissionais', 'filters', 'especialidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $especialidades = Especialidade::ativos()->orderBy('descricao')->get();
        return view('profissionais.create', compact('especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar se há erro de validação CPF do JavaScript
        if ($request->has('cpf_validation_error')) {
            return back()->withInput()
                ->with('validation_message', $request->cpf_validation_error);
        }

        // Limpar CPF e telefone antes da validação
        $cpfLimpo = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;
        $telefoneLimpo = $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null;

        // Atualizar o request com os valores limpos para validação
        $request->merge([
            'telefone' => $telefoneLimpo
        ]);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                new ValidCpf(),
                Rule::unique('profissional', 'cpf')->where(function ($query) use ($cpfLimpo) {
                    return $query->where('cpf', $cpfLimpo)
                        ->whereNull('deleted_at');
                })
            ],
            'especialidade_id' => 'required|exists:especialidade,id',
            'registro_conselho' => 'nullable|string|max:50',
            'sexo' => 'nullable|in:1,2',
            'nascimento_em' => 'nullable|date|before:today',
            'telefone' => 'nullable|string|size:11',
            'email' => 'nullable|email|max:255',
            'chave_pix' => 'nullable|string|max:255'
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'especialidade_id.required' => 'A especialidade é obrigatória.',
            'especialidade_id.exists' => 'Especialidade inválida.',
            'sexo.in' => 'Sexo deve ser Masculino ou Feminino.',
            'nascimento_em.before' => 'A data de nascimento deve ser anterior ao dia de hoje.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'nascimento_em.date' => 'A data de nascimento deve ser uma data válida.',
            'telefone.size' => 'O telefone deve conter exatamente 11 dígitos.'
        ]);

        try {
            DB::beginTransaction();

            // Obter location_id do tenant atual
            $locationId = session('location_id');
            $tenantId = session('tenant_id');
            $userLocations = session('user_locations', []);

            // Se não tem location_id, buscar a primeira location do tenant
            if (!$locationId && $tenantId) {
                $firstLocation = collect($userLocations)
                    ->where('tenant_id', $tenantId)
                    ->first();
                $locationId = $firstLocation['location_id'] ?? null;
            }

            if (!$locationId) {
                return back()->withInput()
                    ->with('error', 'Nenhuma localização disponível para criar o profissional.');
            }

            $profissional = Profissional::create([
                'location_id' => $locationId,
                'nome' => $request->nome,
                'cpf' => $cpfLimpo,
                'especialidade_id' => $request->especialidade_id,
                'registro_conselho' => $request->registro_conselho,
                'sexo' => $request->sexo,
                'nascimento_em' => $request->nascimento_em,
                'telefone' => $telefoneLimpo,
                'email' => $request->email,
                'chave_pix' => $request->chave_pix,
                'ativo' => true
            ]);

            DB::commit();

            return redirect()->route('profissionais.index')
                ->with('success', 'Profissional cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao cadastrar profissional: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Profissional $profissional)
    {
        // Verificar se o profissional pertence ao tenant atual
        $this->checkTenantAccess($profissional);
        
        $profissional->load('especialidade');
        return view('profissionais.show', compact('profissional'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profissional $profissional)
    {
        // Verificar se o profissional pertence ao tenant atual
        $this->checkTenantAccess($profissional);
        
        $especialidades = Especialidade::ativos()->orderBy('descricao')->get();
        return view('profissionais.edit', compact('profissional', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profissional $profissional)
    {
        // Verificar se o profissional pertence ao tenant atual
        $this->checkTenantAccess($profissional);
        
        // Verificar se há erro de validação CPF do JavaScript
        if ($request->has('cpf_validation_error')) {
            return back()->withInput()
                ->with('validation_message', $request->cpf_validation_error);
        }
        
        // Limpar CPF e telefone antes da validação
        $cpfLimpo = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;
        $telefoneLimpo = $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null;

        // Atualizar o request com os valores limpos para validação
        $request->merge([
            'telefone' => $telefoneLimpo
        ]);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'string',
                new ValidCpf(),
                Rule::unique('profissional', 'cpf')->ignore($profissional->id)->where(function ($query) use ($cpfLimpo) {
                    return $query->where('cpf', $cpfLimpo)
                        ->whereNull('deleted_at');
                })
            ],
            'especialidade_id' => 'required|exists:especialidade,id',
            'registro_conselho' => 'nullable|string|max:50',
            'sexo' => 'nullable|in:1,2',
            'nascimento_em' => 'nullable|date|before:today',
            'telefone' => 'nullable|string|size:11',
            'email' => 'nullable|email|max:255',
            'chave_pix' => 'nullable|string|max:255'
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'especialidade_id.required' => 'A especialidade é obrigatória.',
            'especialidade_id.exists' => 'Especialidade inválida.',
            'sexo.in' => 'Sexo deve ser Masculino ou Feminino.',
            'nascimento_em.before' => 'A data de nascimento deve ser anterior ao dia de hoje.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'nascimento_em.date' => 'A data de nascimento deve ser uma data válida.',
            'telefone.size' => 'O telefone deve conter exatamente 11 dígitos.'
        ]);

        try {
            DB::beginTransaction();

            // Manter o location_id original (não permitir mudança de tenant)
            $updateData = [
                'nome' => $request->nome,
                'cpf' => $cpfLimpo,
                'especialidade_id' => $request->especialidade_id,
                'registro_conselho' => $request->registro_conselho,
                'sexo' => $request->sexo,
                'nascimento_em' => $request->nascimento_em,
                'telefone' => $telefoneLimpo,
                'email' => $request->email,
                'chave_pix' => $request->chave_pix,
                'ativo' => $request->has('ativo')
            ];

            $profissional->update($updateData);

            DB::commit();

            return redirect()->route('profissionais.index')
                ->with('success', 'Profissional atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar profissional: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profissional $profissional)
    {
        // Verificar se o profissional pertence ao tenant atual
        $this->checkTenantAccess($profissional);
        
        try {
            DB::beginTransaction();

            $profissional->delete();

            DB::commit();

            return redirect()->route('profissionais.index')
                ->with('success', 'Profissional excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erro ao excluir profissional: ' . $e->getMessage());
        }
    }

    /**
     * Busca profissionais via AJAX
     */
    public function search(Request $request)
    {
        // Obter location_ids do tenant atual
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (empty($locationIds)) {
            return response()->json([]);
        }

        $search = $request->get('q');
        $cpfLimpo = preg_replace('/[^0-9]/', '', $search);

        $profissionais = Profissional::where('ativo', true)
            ->whereIn('location_id', $locationIds)
            ->where(function ($query) use ($search, $cpfLimpo) {
                $query->where('nome', 'ILIKE', "%{$search}%")
                    ->orWhere('registro_conselho', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");

                if ($cpfLimpo && strlen($cpfLimpo) >= 3) {
                    $query->orWhere('cpf', 'LIKE', "%{$cpfLimpo}%");
                }
            })
            ->with('especialidade')
            ->limit(10)
            ->get()
            ->map(function ($profissional) {
                return [
                    'id' => $profissional->id,
                    'nome' => $profissional->nome,
                    'cpf' => $profissional->cpf_formatado,
                    'especialidade' => $profissional->especialidade->descricao ?? '',
                    'registro_conselho' => $profissional->registro_conselho,
                    'telefone' => $profissional->telefone_formatado,
                    'email' => $profissional->email
                ];
            });

        return response()->json($profissionais);
    }

    /**
     * Ativar/Desativar profissional
     */
    public function toggleStatus(Profissional $profissional)
    {
        // Verificar se o profissional pertence ao tenant atual
        $this->checkTenantAccess($profissional);
        
        try {
            DB::beginTransaction();

            $novoStatus = !$profissional->ativo;
            $profissional->update(['ativo' => $novoStatus]);

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => $novoStatus,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica se o profissional pertence ao tenant atual
     */
    private function checkTenantAccess(Profissional $profissional)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        // Buscar location_ids do tenant atual
        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        // Verificar se o profissional pertence a uma location do tenant atual
        if (empty($locationIds) || !in_array($profissional->location_id, $locationIds)) {
            abort(403, 'Acesso negado. Este profissional não pertence ao seu tenant.');
        }
    }
}

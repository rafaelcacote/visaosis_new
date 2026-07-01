<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Http\Requests\PrescriptionFormRequest;
use App\Models\Pessoa;
use App\Models\PedidoVenda;
use App\Models\Prescricao;
use App\Models\OrdemServico;
use App\Rules\ValidCpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PessoaController extends Controller
{
    /**
     * Lista de pacientes (pessoas).
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'todos');

        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        // Determina as locations válidas para o tenant atual
        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        // Se não houver tenant ou locations válidas, retorna lista vazia
        if (! $tenantId || empty($locationIds)) {
            $pessoas = Pessoa::whereRaw('1 = 0')->paginate(15);

            return view('pessoas.index', compact('pessoas', 'search', 'status'));
        }

        $query = Pessoa::where('tenant_id', $tenantId)
            ->whereIn('location_id', $locationIds);

        if ($search) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $cpfLimpo) {
                $q->where('nome', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");

                // Se a busca parece ser um CPF (só números e tem pelo menos 3 dígitos)
                if ($cpfLimpo && strlen($cpfLimpo) >= 3) {
                    $q->orWhere('cpf', 'LIKE', "%{$cpfLimpo}%");
                }
            });
        }

        if ($status !== 'todos') {
            $query->where('ativo', $status === 'ativo');
        }

        $pessoas = $query->orderBy('nome')->paginate(15);

        return view('pessoas.index', compact('pessoas', 'search', 'status'));
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        return view('pessoas.create');
    }

    /**
     * Armazena um novo paciente.
     */
    public function store(Request $request)
    {
        // Verificar se há erro de validação CPF do JavaScript
        if ($request->has('cpf_validation_error')) {
            return back()->withInput()
                ->with('validation_message', $request->cpf_validation_error);
        }

        // Limpar CPF, telefone e CEP antes da validação
        $cpfLimpo = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;
        $telefoneLimpo = $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null;
        $cepLimpo = $request->cep ? preg_replace('/[^0-9]/', '', $request->cep) : null;

        // Atualizar o request com os valores limpos para validação
        $request->merge([
            'telefone' => $telefoneLimpo,
            'cep' => $cepLimpo,
        ]);

        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        if (! $tenantId) {
            return back()->withInput()
                ->with('error', 'Nenhum tenant disponível para criar o paciente.');
        }

        // Se não tem location_id, buscar a primeira location do tenant
        if (! $locationId && $tenantId) {
            $firstLocation = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->first();
            $locationId = $firstLocation['location_id'] ?? null;
        }

        if (! $locationId) {
            return back()->withInput()
                ->with('error', 'Nenhuma localização disponível para criar o paciente.');
        }

        $cpfRules = ['nullable', 'string', new ValidCpf];
        if ($cpfLimpo) {
            $cpfRules[] = Rule::unique('pessoa', 'cpf')
                ->where(function ($query) use ($cpfLimpo, $tenantId) {
                    return $query->where('tenant_id', $tenantId)
                        ->where('cpf', $cpfLimpo)
                        ->whereNull('deleted_at');
                });
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'required|string|max:255',
            'cpf' => $cpfRules,
            'nome_mae' => 'nullable|string|max:255',
            'nome_pai' => 'nullable|string|max:255',
            'sexo' => 'nullable|in:1,2',
            'nascimento_em' => 'nullable|date|before:today',
            'deficiencia' => 'nullable|string|max:200',
            'cep' => 'nullable|string|size:8',
            'logradouro' => 'nullable|string|max:100',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'localidade' => 'nullable|string|max:50',
            'uf' => 'nullable|string|max:50',
            'numero' => 'nullable|string|max:25',
            'telefone' => 'nullable|string|min:10|max:11',
            'email' => 'nullable|email|max:255',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'apelido.required' => 'O apelido é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'sexo.in' => 'Sexo deve ser Masculino ou Feminino.',
            'nascimento_em.before' => 'A data de nascimento deve ser anterior ao dia de hoje.',
            'nascimento_em.date' => 'A data de nascimento deve ser uma data válida.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'cep.size' => 'O CEP deve conter exatamente 8 dígitos.',
            'telefone.min' => 'O telefone deve conter pelo menos 10 dígitos.',
            'telefone.max' => 'O telefone deve conter no máximo 11 dígitos.',
        ]);

        try {
            DB::beginTransaction();

            Pessoa::create([
                'nome' => $request->nome,
                'apelido' => $request->apelido,
                'cpf' => $cpfLimpo,
                'nome_mae' => $request->nome_mae,
                'nome_pai' => $request->nome_pai,
                'sexo' => $request->sexo,
                'nascimento_em' => $request->nascimento_em,
                'deficiencia' => $request->deficiencia,
                'cep' => $cepLimpo,
                'logradouro' => $request->logradouro,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'localidade' => $request->localidade,
                'uf' => $request->uf,
                'numero' => $request->numero,
                'telefone' => $telefoneLimpo,
                'email' => $request->email,
                'ativo' => true,
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('pessoas.index')
                ->with('success', 'Paciente cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao cadastrar paciente: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o paciente.
     */
    public function show(Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        return view('pessoas.show', compact('pessoa'));
    }

    public function receitas(Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        $query = \App\Models\Prescricao::with(['consulta.profissional.especialidade'])
            ->where('pessoa_paciente_id', $pessoa->id)
            ->whereNull('deleted_at');

        $tenantId = session('tenant_id');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $locationId = session('location_id');
        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $prescricoes = $query->orderByDesc('created_at')->get();

        return view('pessoas.receitas', compact('pessoa', 'prescricoes'));
    }

    public function vendas(Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        $query = PedidoVenda::with(['cliente', 'itens.produto'])
            ->where('pessoa_cliente_id', $pessoa->id)
            ->whereNull('deleted_at');

        $tenantId = session('tenant_id');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $locationId = session('location_id');
        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $vendas = $query->orderByDesc('data_pedido')->get();

        $ordensServicoPorVenda = OrdemServico::whereIn('pedido_id', $vendas->pluck('id'))
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('pedido_id');

        return view('pessoas.vendas', compact('pessoa', 'vendas', 'ordensServicoPorVenda'));
    }

    /**
     * Formulário de edição.
     */
    public function edit(Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        return view('pessoas.edit', compact('pessoa'));
    }

    /**
     * Atualiza um paciente.
     */
    public function update(Request $request, Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        // Verificar se há erro de validação CPF do JavaScript
        if ($request->has('cpf_validation_error')) {
            return back()->withInput()
                ->with('validation_message', $request->cpf_validation_error);
        }

        // Limpar CPF, telefone e CEP antes da validação
        $cpfLimpo = $request->cpf ? preg_replace('/[^0-9]/', '', $request->cpf) : null;
        $telefoneLimpo = $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null;
        $cepLimpo = $request->cep ? preg_replace('/[^0-9]/', '', $request->cep) : null;

        // Atualizar o request com os valores limpos para validação
        $request->merge([
            'telefone' => $telefoneLimpo,
            'cep' => $cepLimpo,
        ]);

        $tenantId = session('tenant_id');

        $cpfRules = ['nullable', 'string', new ValidCpf];
        if ($cpfLimpo) {
            $cpfRules[] = Rule::unique('pessoa', 'cpf')
                ->ignore($pessoa->id)
                ->where(function ($query) use ($cpfLimpo, $tenantId) {
                    return $query->where('tenant_id', $tenantId)
                        ->where('cpf', $cpfLimpo)
                        ->whereNull('deleted_at');
                });
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'required|string|max:255',
            'cpf' => $cpfRules,
            'nome_mae' => 'nullable|string|max:255',
            'nome_pai' => 'nullable|string|max:255',
            'sexo' => 'nullable|in:1,2',
            'nascimento_em' => 'nullable|date|before:today',
            'deficiencia' => 'nullable|string|max:200',
            'cep' => 'nullable|string|size:8',
            'logradouro' => 'nullable|string|max:100',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'localidade' => 'nullable|string|max:50',
            'uf' => 'nullable|string|max:50',
            'numero' => 'nullable|string|max:25',
            'telefone' => 'nullable|string|min:10|max:11',
            'email' => 'nullable|email|max:255',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'apelido.required' => 'O apelido é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'sexo.in' => 'Sexo deve ser Masculino ou Feminino.',
            'nascimento_em.before' => 'A data de nascimento deve ser anterior ao dia de hoje.',
            'nascimento_em.date' => 'A data de nascimento deve ser uma data válida.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'cep.size' => 'O CEP deve conter exatamente 8 dígitos.',
            'telefone.min' => 'O telefone deve conter pelo menos 10 dígitos.',
            'telefone.max' => 'O telefone deve conter no máximo 11 dígitos.',
        ]);

        try {
            DB::beginTransaction();

            $pessoa->update([
                'nome' => $request->nome,
                'apelido' => $request->apelido,
                'cpf' => $cpfLimpo,
                'nome_mae' => $request->nome_mae,
                'nome_pai' => $request->nome_pai,
                'sexo' => $request->sexo,
                'nascimento_em' => $request->nascimento_em,
                'deficiencia' => $request->deficiencia,
                'cep' => $cepLimpo,
                'logradouro' => $request->logradouro,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'localidade' => $request->localidade,
                'uf' => $request->uf,
                'numero' => $request->numero,
                'telefone' => $telefoneLimpo,
                'email' => $request->email,
                'ativo' => $request->has('ativo'),
            ]);

            DB::commit();

            return back()->with('success', 'Paciente atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar paciente: ' . $e->getMessage());
        }
    }

    /**
     * Remove um paciente.
     */
    public function destroy(Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        try {
            DB::beginTransaction();

            $pessoa->delete();

            DB::commit();

            return redirect()->route('pessoas.index')
                ->with('success', 'Paciente excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erro ao excluir paciente: ' . $e->getMessage());
        }
    }

    /**
     * Busca pacientes via AJAX (para selects/autocomplete).
     */
    public function search(Request $request)
    {
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

        if (! $tenantId || empty($locationIds)) {
            return response()->json([]);
        }

        $search = $request->get('q');
        $cpfLimpo = preg_replace('/[^0-9]/', '', $search);

        $pessoas = Pessoa::where('ativo', true)
            ->where('tenant_id', $tenantId)
            ->whereIn('location_id', $locationIds)
            ->where(function ($query) use ($search, $cpfLimpo) {
                $query->where('nome', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");

                if ($cpfLimpo && strlen($cpfLimpo) >= 3) {
                    $query->orWhere('cpf', 'LIKE', "%{$cpfLimpo}%");
                }
            })
            ->orderBy('nome')
            ->limit(10)
            ->get()
            ->map(function (Pessoa $pessoa) {
                return [
                    'id' => $pessoa->id,
                    'nome' => $pessoa->nome,
                    'cpf' => $pessoa->cpf_formatado,
                    'telefone' => $pessoa->telefone_formatado,
                    'email' => $pessoa->email,
                    'endereco' => $pessoa->endereco_completo,
                ];
            })
            ->values();

        return response()->json($pessoas);
    }

    /**
     * Ativa/desativa paciente.
     */
    public function toggleStatus(Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        try {
            DB::beginTransaction();

            $novoStatus = ! $pessoa->ativo;
            $pessoa->update(['ativo' => $novoStatus]);

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => $novoStatus,
                'message' => 'Status atualizado com sucesso!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Garante que o paciente pertence ao tenant/location atuais.
     */
    private function checkTenantAccess(Pessoa $pessoa): void
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        if (! $tenantId || (int) $pessoa->tenant_id !== (int) $tenantId) {
            abort(403, 'Acesso negado. Este paciente não pertence ao seu tenant.');
        }

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (! empty($locationIds) && $pessoa->location_id !== null && ! in_array($pessoa->location_id, $locationIds)) {
            abort(403, 'Acesso negado. Este paciente não pertence à sua localização.');
        }
    }

    private function normalizeEsferico(?string $value): string
    {
        $raw = is_string($value) ? trim($value) : '';
        if ($raw === '') {
            return '0';
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return '0';
        }

        $upper = str_replace(',', '.', $upper);
        if (preg_match('/^([+-]?)(\d+(?:\.\d{1,2})?)$/', $upper, $m)) {
            $num = (float) $m[2];
            if (abs($num) < 0.0000001) {
                return '0';
            }

            $sign = $m[1] === '-' ? '-' : '';
            $formatted = number_format($num, 2, '.', '');
            return $sign . $formatted;
        }

        return $raw;
    }

    private function normalizeCilindrico(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return '0';
        }

        $upper = str_replace(',', '.', $upper);
        if (preg_match('/^([+-]?)(\d+(?:\.\d{1,2})?)$/', $upper, $m)) {
            $num = (float) $m[2];
            if (abs($num) < 0.0000001) {
                return '0';
            }

            $formatted = number_format($num, 2, '.', '');
            return '-' . $formatted;
        }

        return $raw;
    }

    /**
     * Store a new prescription for the patient.
     */
    public function storePrescription(PrescriptionFormRequest $request, Pessoa $pessoa)
    {
        $this->checkTenantAccess($pessoa);

        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $prescricao = \App\Models\Prescricao::create([
                'pessoa_paciente_id' => $pessoa->id,
                'user_id' => auth()->id(),
                'especialista_externo' => $validatedData['especialista_externo'],
                'esfera_od' => $validatedData['od_esferico'],
                'cilindro_od' => $validatedData['od_cilindrico'],
                'eixo_od' => $validatedData['od_eixo'],
                'acuidade_od' => $validatedData['od_acuidade'],
                'dnp_od' => $validatedData['od_dnp'],
                'altura_od' => $validatedData['od_altura'],
                'adicao_od' => $validatedData['od_adicao'],
                'esfera_oe' => $validatedData['oe_esferico'],
                'cilindro_oe' => $validatedData['oe_cilindrico'],
                'eixo_oe' => $validatedData['oe_eixo'],
                'acuidade_oe' => $validatedData['oe_acuidade'],
                'dnp_oe' => $validatedData['oe_dnp'],
                'altura_oe' => $validatedData['oe_altura'],
                'adicao_oe' => $validatedData['oe_adicao'],
                'esfera_od_perto' => $validatedData['od_esferico_perto'] ?? null,
                'cilindro_od_perto' => $validatedData['od_cilindrico_perto'] ?? null,
                'eixo_od_perto' => $validatedData['od_eixo_perto'] ?? null,
                'acuidade_od_perto' => $validatedData['od_acuidade_perto'] ?? null,
                'dnp_od_perto' => $validatedData['od_dnp_perto'] ?? null,
                'altura_od_perto' => $validatedData['od_altura_perto'] ?? null,
                'adicao_od_perto' => $validatedData['od_adicao_perto'] ?? null,
                'esfera_oe_perto' => $validatedData['oe_esferico_perto'] ?? null,
                'cilindro_oe_perto' => $validatedData['oe_cilindrico_perto'] ?? null,
                'eixo_oe_perto' => $validatedData['oe_eixo_perto'] ?? null,
                'acuidade_oe_perto' => $validatedData['oe_acuidade_perto'] ?? null,
                'dnp_oe_perto' => $validatedData['oe_dnp_perto'] ?? null,
                'altura_oe_perto' => $validatedData['oe_altura_perto'] ?? null,
                'adicao_oe_perto' => $validatedData['oe_adicao_perto'] ?? null,
                'tipo_lente' => $validatedData['tipo_lente'],
                'validade_dias' => $validatedData['validade_dias'],
                'diagnostico' => $validatedData['diagnostico'],
                'recomendacoes' => $validatedData['recomendacoes'],
                'observacoes' => $validatedData['observacoes_receita'],
                'tenant_id' => AuthHelper::tenantId(),
                'location_id' => AuthHelper::locationId(),
                'ativo' => true,
            ]);

            DB::commit();

            return redirect()->route('pessoas.receitas', $pessoa->id)
                ->with('success', 'Receita cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Erro ao cadastrar receita: ' . $e->getMessage());
        }
    }

    public function editPrescription(Pessoa $pessoa, Prescricao $prescricao)
    {
        $this->checkTenantAccess($pessoa);
        $this->checkPrescriptionAccess($pessoa, $prescricao);

        if (! $prescricao->especialista_externo) {
            abort(403, 'Apenas receitas de especialista externo podem ser editadas.');
        }

        $query = Prescricao::with(['consulta.profissional.especialidade'])
            ->where('pessoa_paciente_id', $pessoa->id)
            ->whereNull('deleted_at');

        $tenantId = session('tenant_id');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $locationId = session('location_id');
        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $prescricoes = $query->orderByDesc('created_at')->get();
        $editPrescricao = $prescricao;

        return view('pessoas.receitas', compact('pessoa', 'prescricoes', 'editPrescricao'));
    }

    public function updatePrescription(PrescriptionFormRequest $request, Pessoa $pessoa, Prescricao $prescricao)
    {
        $this->checkTenantAccess($pessoa);
        $this->checkPrescriptionAccess($pessoa, $prescricao);

        if (! $prescricao->especialista_externo) {
            abort(403, 'Apenas receitas de especialista externo podem ser editadas.');
        }

        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $prescricao->update([
                'especialista_externo' => $validatedData['especialista_externo'],
                'esfera_od' => $validatedData['od_esferico'],
                'cilindro_od' => $validatedData['od_cilindrico'],
                'eixo_od' => $validatedData['od_eixo'],
                'acuidade_od' => $validatedData['od_acuidade'],
                'dnp_od' => $validatedData['od_dnp'],
                'altura_od' => $validatedData['od_altura'],
                'adicao_od' => $validatedData['od_adicao'],
                'esfera_oe' => $validatedData['oe_esferico'],
                'cilindro_oe' => $validatedData['oe_cilindrico'],
                'eixo_oe' => $validatedData['oe_eixo'],
                'acuidade_oe' => $validatedData['oe_acuidade'],
                'dnp_oe' => $validatedData['oe_dnp'],
                'altura_oe' => $validatedData['oe_altura'],
                'adicao_oe' => $validatedData['oe_adicao'],
                'esfera_od_perto' => $validatedData['od_esferico_perto'] ?? null,
                'cilindro_od_perto' => $validatedData['od_cilindrico_perto'] ?? null,
                'eixo_od_perto' => $validatedData['od_eixo_perto'] ?? null,
                'acuidade_od_perto' => $validatedData['od_acuidade_perto'] ?? null,
                'dnp_od_perto' => $validatedData['od_dnp_perto'] ?? null,
                'altura_od_perto' => $validatedData['od_altura_perto'] ?? null,
                'adicao_od_perto' => $validatedData['od_adicao_perto'] ?? null,
                'esfera_oe_perto' => $validatedData['oe_esferico_perto'] ?? null,
                'cilindro_oe_perto' => $validatedData['oe_cilindrico_perto'] ?? null,
                'eixo_oe_perto' => $validatedData['oe_eixo_perto'] ?? null,
                'acuidade_oe_perto' => $validatedData['oe_acuidade_perto'] ?? null,
                'dnp_oe_perto' => $validatedData['oe_dnp_perto'] ?? null,
                'altura_oe_perto' => $validatedData['oe_altura_perto'] ?? null,
                'adicao_oe_perto' => $validatedData['oe_adicao_perto'] ?? null,
                'tipo_lente' => $validatedData['tipo_lente'],
                'validade_dias' => $validatedData['validade_dias'],
                'diagnostico' => $validatedData['diagnostico'],
                'recomendacoes' => $validatedData['recomendacoes'],
                'observacoes' => $validatedData['observacoes_receita'],
            ]);

            DB::commit();

            return redirect()->route('pessoas.receitas', $pessoa->id)
                ->with('success', 'Receita atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Erro ao atualizar receita: ' . $e->getMessage());
        }
    }

    private function checkPrescriptionAccess(Pessoa $pessoa, Prescricao $prescricao): void
    {
        if ((int) $prescricao->pessoa_paciente_id !== (int) $pessoa->id) {
            abort(404);
        }

        $tenantId = session('tenant_id');
        if ($tenantId && (int) $prescricao->tenant_id !== (int) $tenantId) {
            abort(403, 'Acesso negado. Esta receita não pertence ao seu tenant.');
        }

        $locationId = session('location_id');
        if ($locationId && $prescricao->location_id !== null && (int) $prescricao->location_id !== (int) $locationId) {
            abort(403, 'Acesso negado. Esta receita não pertence à sua localização.');
        }
    }
}

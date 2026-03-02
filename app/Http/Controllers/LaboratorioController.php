<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratorio;
use App\Rules\ValidCnpj;
use Illuminate\Support\Facades\DB;

class LaboratorioController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
            'status' => $request->get('status', ''),
        ];

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

        if (empty($tenantId)) {
            $laboratorios = Laboratorio::whereRaw('1 = 0')->paginate(15);
            return view('laboratorios.index', compact('laboratorios', 'filters'));
        }

        // Detectar o driver do banco de dados para usar ILIKE (PostgreSQL) ou LIKE (MySQL)
        $driver = DB::connection()->getDriverName();
        $isPostgres = ($driver === 'pgsql');

        $query = Laboratorio::where('tenant_id', $tenantId);

        // Aplicar filtro de location
        if (!empty($locationIds)) {
            $query->where(function ($q) use ($locationIds) {
                $q->whereIn('location_id', $locationIds)
                    ->orWhereNull('location_id');
            });
        }

        // Aplicar busca ANTES do filtro de status para garantir que funcione
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            
            if (!empty($search)) {
                $cnpjLimpo = preg_replace('/[^0-9]/', '', $search);
                $telefoneLimpo = preg_replace('/[^0-9]/', '', $search);
                
                $query->where(function ($q) use ($search, $cnpjLimpo, $telefoneLimpo, $isPostgres) {
                    // Buscar por razão social (case insensitive)
                    if ($isPostgres) {
                        $q->where('razao_social', 'ILIKE', "%{$search}%");
                    } else {
                        $q->whereRaw('LOWER(razao_social) LIKE ?', ['%' . mb_strtolower($search, 'UTF-8') . '%']);
                    }
                    
                    // Buscar por nome fantasia (case insensitive)
                    if ($isPostgres) {
                        $q->orWhere('nome_fantasia', 'ILIKE', "%{$search}%");
                    } else {
                        $q->orWhereRaw('LOWER(nome_fantasia) LIKE ?', ['%' . mb_strtolower($search, 'UTF-8') . '%']);
                    }
                    
                    // Buscar por email (case insensitive)
                    if ($isPostgres) {
                        $q->orWhere('email', 'ILIKE', "%{$search}%");
                    } else {
                        $q->orWhereRaw('LOWER(email) LIKE ?', ['%' . mb_strtolower($search, 'UTF-8') . '%']);
                    }
                    
                    // Buscar por telefone (apenas números)
                    if (!empty($telefoneLimpo) && strlen($telefoneLimpo) >= 3) {
                        $q->orWhere('telefone', 'LIKE', "%{$telefoneLimpo}%");
                    }
                    
                    // Buscar por CNPJ (apenas números)
                    if (!empty($cnpjLimpo) && strlen($cnpjLimpo) >= 3) {
                        $q->orWhere('cnpj', 'LIKE', "%{$cnpjLimpo}%");
                    }
                });
            }
        }

        // Aplicar filtro de status DEPOIS da busca
        if ($filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('ativo', $filters['status'] == '1');
        }

        // Debug temporário - remover depois
        \Log::info('Query de Laboratórios', [
            'search' => $filters['search'] ?? '',
            'status' => $filters['status'] ?? '',
            'status_empty' => empty($filters['status']),
            'tenant_id' => $tenantId,
            'location_ids' => $locationIds,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $laboratorios = $query->orderBy('razao_social')->paginate(15);

        // Debug temporário - remover depois
        \Log::info('Resultado da busca', [
            'total' => $laboratorios->total(),
            'count' => $laboratorios->count(),
            'has_results' => $laboratorios->count() > 0,
        ]);

        return view('laboratorios.index', compact('laboratorios', 'filters'));
    }

    public function create()
    {
        return view('laboratorios.create');
    }

    public function store(Request $request)
    {
        $locationId = session('location_id');
        $tenantId = session('tenant_id');
        $userLocations = session('user_locations', []);

        if (!$locationId && $tenantId) {
            $firstLocation = collect($userLocations)->where('tenant_id', $tenantId)->first();
            $locationId = $firstLocation['location_id'] ?? null;
        }

        if (!$tenantId) {
            return back()->withInput()
                ->with('error', 'Nenhum tenant disponível para criar o laboratório.');
        }

        $cnpjLimpo = $request->cnpj ? preg_replace('/[^0-9]/', '', $request->cnpj) : '';

        $request->validate([
            'cnpj' => [
                'required',
                'string',
                new ValidCnpj(),
                function ($attribute, $value, $fail) use ($tenantId, $cnpjLimpo) {
                    if (Laboratorio::where('tenant_id', $tenantId)->where('cnpj', $cnpjLimpo)->whereNull('deleted_at')->exists()) {
                        $fail('Este CNPJ já está cadastrado.');
                    }
                }
            ],
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'chave_pix' => 'nullable|string|max:255',
            'ativo' => 'nullable|boolean'
        ], [
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        try {
            DB::beginTransaction();

            Laboratorio::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'cnpj' => $cnpjLimpo,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'telefone' => $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null,
                'email' => $request->email,
                'chave_pix' => $request->chave_pix,
                'ativo' => $request->has('ativo') ? true : true
            ]);

            DB::commit();

            return redirect()->route('laboratorios.index')
                ->with('success', 'Laboratório cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao cadastrar laboratório: ' . $e->getMessage());
        }
    }

    public function show(Laboratorio $laboratorio)
    {
        $this->checkTenantAccess($laboratorio);
        return view('laboratorios.show', compact('laboratorio'));
    }

    public function edit(Laboratorio $laboratorio)
    {
        $this->checkTenantAccess($laboratorio);
        return view('laboratorios.edit', compact('laboratorio'));
    }

    public function update(Request $request, Laboratorio $laboratorio)
    {
        $this->checkTenantAccess($laboratorio);

        $cnpjLimpo = $request->cnpj ? preg_replace('/[^0-9]/', '', $request->cnpj) : '';

        $request->validate([
            'cnpj' => [
                'required',
                'string',
                new ValidCnpj(),
                function ($attribute, $value, $fail) use ($laboratorio, $cnpjLimpo) {
                    if (Laboratorio::where('tenant_id', $laboratorio->tenant_id)->where('cnpj', $cnpjLimpo)->whereNull('deleted_at')->where('id', '!=', $laboratorio->id)->exists()) {
                        $fail('Este CNPJ já está cadastrado.');
                    }
                }
            ],
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'chave_pix' => 'nullable|string|max:255',
            'ativo' => 'nullable|boolean'
        ], [
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'razao_social.required' => 'A razão social é obrigatória.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        try {
            DB::beginTransaction();

            $laboratorio->update([
                'cnpj' => $cnpjLimpo,
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'telefone' => $request->telefone ? preg_replace('/[^0-9]/', '', $request->telefone) : null,
                'email' => $request->email,
                'chave_pix' => $request->chave_pix,
                'ativo' => $request->has('ativo') && $request->ativo == '1'
            ]);

            DB::commit();

            return redirect()->route('laboratorios.index')
                ->with('success', 'Laboratório atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erro ao atualizar laboratório: ' . $e->getMessage());
        }
    }

    public function destroy(Laboratorio $laboratorio)
    {
        $this->checkTenantAccess($laboratorio);

        try {
            DB::beginTransaction();
            $laboratorio->delete();
            DB::commit();

            return redirect()->route('laboratorios.index')
                ->with('success', 'Laboratório excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (
                strpos($e->getMessage(), 'Foreign key violation') !== false ||
                strpos($e->getMessage(), 'foreign key constraint') !== false
            ) {
                return back()->with('error', 'Não é possível excluir este laboratório porque está sendo utilizado.');
            }

            return back()->with('error', 'Erro ao excluir laboratório: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Laboratorio $laboratorio)
    {
        $this->checkTenantAccess($laboratorio);

        try {
            DB::beginTransaction();
            $novoStatus = !$laboratorio->ativo;
            $laboratorio->update(['ativo' => $novoStatus]);
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

    private function checkTenantAccess(Laboratorio $laboratorio)
    {
        $tenantId = session('tenant_id');
        if (!$tenantId || (int) $laboratorio->tenant_id !== (int) $tenantId) {
            abort(403, 'Acesso negado. Este laboratório não pertence ao seu tenant.');
        }

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

        if (!empty($locationIds) && $laboratorio->location_id !== null && !in_array($laboratorio->location_id, $locationIds)) {
            abort(403, 'Acesso negado. Este laboratório não pertence à sua localização.');
        }
    }
}

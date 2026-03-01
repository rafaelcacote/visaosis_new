<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
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
            $categorias = Categoria::whereRaw('1 = 0')->paginate(15);
            return view('categorias.index', compact('categorias', 'filters'));
        }

        $query = Categoria::whereIn('location_id', $locationIds);

        // Filtro de busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('descricao', 'ILIKE', "%{$search}%");
        }

        // Filtro de status
        if ($filters['status'] !== '') {
            $query->where('ativo', $filters['status'] == '1');
        }

        // Paginação
        $categorias = $query->orderBy('descricao')
            ->paginate(15);

        return view('categorias.index', compact('categorias', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        if (!$tenantId) {
            return back()->withInput()
                ->with('error', 'Nenhum tenant disponível para criar a categoria.');
        }

        if (!$locationId) {
            return back()->withInput()
                ->with('error', 'Nenhuma localização disponível para criar a categoria.');
        }

        $request->validate([
            'descricao' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($locationId, $tenantId) {
                    $exists = Categoria::whereRaw('LOWER(descricao) = ?', [strtolower($value)])
                        ->where('tenant_id', $tenantId)
                        ->where('location_id', $locationId)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma categoria com esta descrição nesta localização.');
                    }
                }
            ],
            'ativo' => 'nullable|boolean'
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $categoria = Categoria::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'descricao' => $request->descricao,
                'ativo' => $request->has('ativo') ? true : true
            ]);

            DB::commit();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoria cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao cadastrar categoria: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        // Verificar se a categoria pertence ao tenant atual
        $this->checkTenantAccess($categoria);
        
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        // Verificar se a categoria pertence ao tenant atual
        $this->checkTenantAccess($categoria);
        
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        // Verificar se a categoria pertence ao tenant atual
        $this->checkTenantAccess($categoria);

        $request->validate([
            'descricao' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($categoria) {
                    $exists = Categoria::whereRaw('LOWER(descricao) = ?', [strtolower($value)])
                        ->where('location_id', $categoria->location_id)
                        ->where('id', '!=', $categoria->id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma categoria com esta descrição nesta localização.');
                    }
                }
            ],
            'ativo' => 'nullable|boolean'
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $categoria->update([
                'descricao' => $request->descricao,
                'ativo' => $request->has('ativo') && $request->ativo == '1'
            ]);

            DB::commit();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoria atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar categoria: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        // Verificar se a categoria pertence ao tenant atual
        $this->checkTenantAccess($categoria);
        
        try {
            DB::beginTransaction();

            $categoria->delete();

            DB::commit();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoria excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Verificar se é erro de foreign key
            if (
                strpos($e->getMessage(), 'Foreign key violation') !== false ||
                strpos($e->getMessage(), 'foreign key constraint') !== false
            ) {
                return back()->with('error', 'Não é possível excluir esta categoria porque ela está sendo usada.');
            }

            return back()->with('error', 'Erro ao excluir categoria: ' . $e->getMessage());
        }
    }

    /**
     * Ativar/Desativar categoria
     */
    public function toggleStatus(Categoria $categoria)
    {
        // Verificar se a categoria pertence ao tenant atual
        $this->checkTenantAccess($categoria);
        
        try {
            DB::beginTransaction();

            $novoStatus = !$categoria->ativo;
            $categoria->update(['ativo' => $novoStatus]);

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
     * Verifica se a categoria pertence ao tenant atual
     */
    private function checkTenantAccess(Categoria $categoria)
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

        // Verificar se a categoria pertence a uma location do tenant atual
        if (empty($locationIds) || !in_array($categoria->location_id, $locationIds)) {
            abort(403, 'Acesso negado. Esta categoria não pertence ao seu tenant.');
        }
    }
}

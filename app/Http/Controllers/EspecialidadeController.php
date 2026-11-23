<?php

namespace App\Http\Controllers;

use App\Models\Especialidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EspecialidadeController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
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
            $especialidades = Especialidade::whereRaw('1 = 0')->paginate(15);
            return view('especialidades.index', compact('especialidades', 'filters'));
        }

        $query = Especialidade::whereIn('location_id', $locationIds);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('descricao', 'ILIKE', "%{$search}%");
        }

        $especialidades = $query->orderBy('descricao')->paginate(15);

        return view('especialidades.index', compact('especialidades', 'filters'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

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
                ->with('error', 'Nenhum tenant disponível para criar a especialidade.');
        }

        if (!$locationId) {
            return back()->withInput()
                ->with('error', 'Nenhuma localização disponível para criar a especialidade.');
        }

        $request->validate([
            'descricao' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($locationId, $tenantId) {
                    $exists = Especialidade::whereRaw('LOWER(descricao) = ?', [strtolower($value)])
                        ->where('tenant_id', $tenantId)
                        ->where('location_id', $locationId)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma especialidade com esta descrição nesta localização.');
                    }
                },
            ],
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            Especialidade::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'descricao' => $request->descricao,
                'ativo' => true,
            ]);

            DB::commit();

            return redirect()->route('especialidades.index')
                ->with('success', 'Especialidade cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao cadastrar especialidade: ' . $e->getMessage());
        }
    }

    public function show(Especialidade $especialidade)
    {
        // Verificar se a especialidade pertence ao tenant atual
        $this->checkTenantAccess($especialidade);

        return view('especialidades.show', compact('especialidade'));
    }

    public function edit(Especialidade $especialidade)
    {
        // Verificar se a especialidade pertence ao tenant atual
        $this->checkTenantAccess($especialidade);

        return view('especialidades.edit', compact('especialidade'));
    }

    public function update(Request $request, Especialidade $especialidade)
    {
        // Verificar se a especialidade pertence ao tenant atual
        $this->checkTenantAccess($especialidade);

        $request->validate([
            'descricao' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($especialidade) {
                    $exists = Especialidade::whereRaw('LOWER(descricao) = ?', [strtolower($value)])
                        ->where('location_id', $especialidade->location_id)
                        ->where('id', '!=', $especialidade->id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('Já existe uma especialidade com esta descrição nesta localização.');
                    }
                },
            ],
        ], [
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
        ]);

        try {
            DB::beginTransaction();

            $especialidade->update([
                'descricao' => $request->descricao,
            ]);

            DB::commit();

            return redirect()->route('especialidades.index')
                ->with('success', 'Especialidade atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar especialidade: ' . $e->getMessage());
        }
    }

    public function destroy(Especialidade $especialidade)
    {
        // Verificar se a especialidade pertence ao tenant atual
        $this->checkTenantAccess($especialidade);

        try {
            DB::beginTransaction();

            $especialidade->delete();

            DB::commit();

            return redirect()->route('especialidades.index')
                ->with('success', 'Especialidade excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (
                strpos($e->getMessage(), 'Foreign key violation') !== false ||
                strpos($e->getMessage(), 'foreign key constraint') !== false
            ) {
                return back()->with('error', 'Não é possível excluir esta especialidade porque ela está sendo usada.');
            }

            return back()->with('error', 'Erro ao excluir especialidade: ' . $e->getMessage());
        }
    }

    /**
     * Verifica se a especialidade pertence ao tenant atual
     */
    private function checkTenantAccess(Especialidade $especialidade)
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

        // Verificar se a especialidade pertence a uma location do tenant atual
        if (empty($locationIds) || !in_array($especialidade->location_id, $locationIds)) {
            abort(403, 'Acesso negado. Esta especialidade não pertence ao seu tenant.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search', ''),
            'status' => $request->get('status', ''),
            'per_page' => $request->get('per_page', 5),
        ];

        // Obter tenant_id e location_ids da sessão
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

        // Se não tem location_ids válidos, retornar vazio
        if (empty($locationIds)) {
            $users = User::whereRaw('1 = 0')->paginate($filters['per_page']);
            return view('users.index', compact('users', 'filters', 'userLocations'));
        }

        // Buscar user_ids que pertencem ao tenant atual através de user_locations
        $userIds = DB::connection('cerberus')
            ->table('seguranca.user_locations')
            ->whereIn('location_id', $locationIds)
            ->where('status', 1)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        if (empty($userIds)) {
            $users = User::whereRaw('1 = 0')->paginate($filters['per_page']);
            return view('users.index', compact('users', 'filters', 'userLocations'));
        }

        $query = User::whereIn('id', $userIds);

        // Filtro de busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('cpf_cnpj', 'ILIKE', "%{$search}%");
            });
        }

        // Filtro de status
        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Paginação
        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($filters['per_page']);

        // Carregar locations para cada usuário (apenas do tenant atual)
        $userIdsResult = $users->pluck('id')->toArray();
        $userLocations = [];
        
        if (!empty($userIdsResult)) {
            $locationsData = DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->join('seguranca.locations', 'user_locations.location_id', '=', 'locations.id')
                ->whereIn('user_locations.user_id', $userIdsResult)
                ->whereIn('user_locations.location_id', $locationIds)
                ->where('user_locations.status', 1)
                ->select('user_locations.user_id', 'locations.name', 'locations.short_name')
                ->get()
                ->groupBy('user_id');
            
            foreach ($locationsData as $userId => $locations) {
                $userLocations[$userId] = $locations;
            }
        }

        return view('users.index', compact('users', 'filters', 'userLocations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUserId = Auth::id();
        $tenantId = session('tenant_id');

        // Buscar locations disponíveis SOMENTE do tenant do usuário logado
        // (usa seguranca.user_locations.tenant_id como fonte de tenant)
        $tenantIds = [];
        if (empty($tenantId) && $currentUserId) {
            try {
                $tenantIds = DB::connection('cerberus')
                    ->table('seguranca.user_locations')
                    ->where('user_id', $currentUserId)
                    ->where('status', 1)
                    ->distinct()
                    ->pluck('tenant_id')
                    ->toArray();
            } catch (\Exception $e) {
                $tenantIds = [];
            }
        }

        $locations = collect([]);
        if ($currentUserId) {
            $locationsQuery = DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->join('seguranca.locations', 'user_locations.location_id', '=', 'locations.id')
                ->where('user_locations.user_id', $currentUserId)
                ->where('user_locations.status', 1)
                ->where('locations.status', 1);

            if (!empty($tenantId)) {
                $locationsQuery->where('user_locations.tenant_id', $tenantId);
            } elseif (!empty($tenantIds)) {
                $locationsQuery->whereIn('user_locations.tenant_id', $tenantIds);
            }

            $locations = $locationsQuery
                ->select('locations.id', 'locations.name', 'locations.short_name')
                ->distinct()
                ->orderBy('locations.name')
                ->get();
        }

        // Buscar perfis disponíveis (se a tabela existir)
        try {
            $systemKey = env('CERBERUS_SYSTEM_KEY');

            $profilesQuery = DB::connection('cerberus')
                ->table('seguranca.profiles')
                ->where('profiles.status', 1);

            // Se o system_key estiver configurado, filtrar perfis do sistema atual
            if (!empty($systemKey)) {
                $profilesQuery
                    ->join('seguranca.systems', 'profiles.system_id', '=', 'systems.id')
                    ->where('systems.system_key', $systemKey);
            }

            $profiles = $profilesQuery
                ->orderBy('profiles.name')
                ->get(['profiles.id', 'profiles.name', 'profiles.short_name', 'profiles.system_id']);
        } catch (\Exception $e) {
            $profiles = collect([]);
        }

        // Buscar sistemas disponíveis (se a tabela existir)
        try {
            $systems = DB::connection('cerberus')
                ->table('seguranca.systems')
                ->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'short_name', 'system_key']);
        } catch (\Exception $e) {
            $systems = collect([]);
        }

        return view('users.create', compact('locations', 'profiles', 'systems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar email único manualmente
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return back()->withErrors(['email' => 'Este email já está em uso.'])->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'cpf_cnpj' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'locations' => 'required|array|min:1',
            'profiles' => 'nullable|array',
            'systems' => 'nullable|array',
            'status' => 'boolean'
        ], [
            'locations.required' => 'Selecione pelo menos uma loja.',
            'locations.min' => 'Selecione pelo menos uma loja.'
        ]);

        try {
            DB::connection('cerberus')->beginTransaction();

            // Criar usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->cpf_cnpj),
                'password' => Hash::make($request->password),
                'status' => $request->has('status') ? 1 : 0,
            ]);

            // Vincular locations
            foreach ($request->locations as $locationId) {
                DB::connection('cerberus')
                    ->table('seguranca.user_locations')
                    ->insert([
                        'user_id' => $user->id,
                        'location_id' => $locationId,
                        'tenant_id' => session('tenant_id', 1), // Usar tenant da sessão
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // Vincular perfis (se existir)
            if ($request->has('profiles')) {
                try {
                    foreach ($request->profiles as $profileId) {
                        DB::connection('cerberus')
                            ->table('seguranca.user_profile')
                            ->insert([
                                'user_id' => $user->id,
                                'profile_id' => $profileId,
                                'status' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                    }
                } catch (\Exception $e) {
                    // Tabela não existe, ignorar
                }
            }

            DB::connection('cerberus')->commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuário criado com sucesso!');
        } catch (\Exception $e) {
            DB::connection('cerberus')->rollBack();
            Log::error('Erro ao criar usuário: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao criar usuário: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        // Verificar se o usuário pertence ao tenant atual
        $this->checkUserTenantAccess($user->id);

        // Buscar locations do usuário (apenas do tenant atual)
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

        $userLocations = DB::connection('cerberus')
            ->table('seguranca.user_locations')
            ->join('seguranca.locations', 'user_locations.location_id', '=', 'locations.id')
            ->where('user_locations.user_id', $user->id)
            ->when(!empty($locationIds), function($query) use ($locationIds) {
                return $query->whereIn('user_locations.location_id', $locationIds);
            })
            ->where('user_locations.status', 1)
            ->select('locations.id', 'locations.name', 'locations.short_name')
            ->get();

        return view('users.show', compact('user', 'userLocations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        // Verificar se o usuário pertence ao tenant atual
        $this->checkUserTenantAccess($user->id);

        $currentUserId = Auth::id();
        $tenantId = session('tenant_id');

        // Buscar locations disponíveis SOMENTE do tenant do usuário logado
        $tenantIds = [];
        if (empty($tenantId) && $currentUserId) {
            try {
                $tenantIds = DB::connection('cerberus')
                    ->table('seguranca.user_locations')
                    ->where('user_id', $currentUserId)
                    ->where('status', 1)
                    ->distinct()
                    ->pluck('tenant_id')
                    ->toArray();
            } catch (\Exception $e) {
                $tenantIds = [];
            }
        }

        // Options (listas disponíveis)
        $locations = collect([]);
        if ($currentUserId) {
            $locationsQuery = DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->join('seguranca.locations', 'user_locations.location_id', '=', 'locations.id')
                ->where('user_locations.user_id', $currentUserId)
                ->where('user_locations.status', 1)
                ->where('locations.status', 1);

            if (!empty($tenantId)) {
                $locationsQuery->where('user_locations.tenant_id', $tenantId);
            } elseif (!empty($tenantIds)) {
                $locationsQuery->whereIn('user_locations.tenant_id', $tenantIds);
            }

            $locations = $locationsQuery
                ->select('locations.id', 'locations.name', 'locations.short_name')
                ->distinct()
                ->orderBy('locations.name')
                ->get();
        }

        try {
            $profiles = DB::connection('cerberus')
                ->table('seguranca.profiles')
                ->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'short_name', 'system_id']);
        } catch (\Exception $e) {
            $profiles = collect([]);
        }

        try {
            $systems = DB::connection('cerberus')
                ->table('seguranca.systems')
                ->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name', 'short_name', 'system_key']);
        } catch (\Exception $e) {
            $systems = collect([]);
        }

        // Checked values (vínculos do usuário editado)
        $userLocationIds = [];
        try {
            $userLocationIds = DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
                ->pluck('location_id')
                ->toArray();
        } catch (\Exception $e) {
            $userLocationIds = [];
        }

        // user_profile (tabela no Cerberus é singular)
        $userProfileIds = [];
        try {
            $userProfileIds = DB::connection('cerberus')
                ->table('seguranca.user_profile')
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->pluck('profile_id')
                ->toArray();
        } catch (\Exception $e) {
            $userProfileIds = [];
        }

        // Sistemas do usuário (derivado dos perfis -> itens -> system)
        $userSystemIds = [];
        try {
            $userSystemIds = DB::connection('cerberus')
                ->table('seguranca.user_profile')
                ->join('seguranca.profile_item', 'user_profile.profile_id', '=', 'profile_item.profile_id')
                ->join('seguranca.items', 'profile_item.item_id', '=', 'items.id')
                ->join('seguranca.systems', 'items.system_id', '=', 'systems.id')
                ->where('user_profile.user_id', $user->id)
                ->where('user_profile.status', 1)
                ->where('items.status', 1)
                ->where('systems.status', 1)
                ->distinct()
                ->pluck('systems.id')
                ->toArray();
        } catch (\Exception $e) {
            $userSystemIds = [];
        }

        return view('users.edit', compact('user', 'locations', 'userLocationIds', 'profiles', 'userProfileIds', 'systems', 'userSystemIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Verificar se o usuário pertence ao tenant atual
        $this->checkUserTenantAccess($user->id);

        // Validar email único manualmente (exceto o próprio usuário)
        $existingUser = User::where('email', $request->email)->where('id', '!=', $id)->first();
        if ($existingUser) {
            return back()->withErrors(['email' => 'Este email já está em uso.'])->withInput();
        }

        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'locations' => 'required|array|min:1',
            'status' => 'boolean'
        ];

        // Apenas validar senha se foi informada
        if ($request->has('password') && !empty($request->password)) {
            $validationRules['password'] = 'string|min:6|confirmed';
        }

        $request->validate($validationRules, [
            'locations.required' => 'Selecione pelo menos uma loja.',
            'locations.min' => 'Selecione pelo menos uma loja.'
        ]);

        try {
            DB::connection('cerberus')->beginTransaction();

            // Atualizar dados do usuário
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->has('status') ? 1 : 0,
            ];

            // Adicionar senha apenas se foi informada
            if ($request->has('password') && !empty($request->password)) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Atualizar locations (remover antigas e adicionar novas)
            DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->where('user_id', $user->id)
                ->delete();

            foreach ($request->locations as $locationId) {
                DB::connection('cerberus')
                    ->table('seguranca.user_locations')
                    ->insert([
                        'user_id' => $user->id,
                        'location_id' => $locationId,
                        'tenant_id' => session('tenant_id', 1),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // Atualizar perfis (Cerberus: tabela "user_profile")
            try {
                DB::connection('cerberus')
                    ->table('seguranca.user_profile')
                    ->where('user_id', $user->id)
                    ->delete();

                if ($request->has('profiles')) {
                    foreach ($request->profiles as $profileId) {
                        DB::connection('cerberus')
                            ->table('seguranca.user_profile')
                            ->insert([
                                'user_id' => $user->id,
                                'profile_id' => $profileId,
                                'status' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                    }
                }
            } catch (\Exception $e) {
                // Tabela não existe, ignorar
            }

            DB::connection('cerberus')->commit();

            return redirect()->route('users.show', $user->id)
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::connection('cerberus')->rollBack();
            Log::error('Erro ao atualizar usuário: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao atualizar usuário: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Verificar se o usuário pertence ao tenant atual
            $this->checkUserTenantAccess($user->id);

            DB::connection('cerberus')->beginTransaction();

            // Remover vínculos de locations
            DB::connection('cerberus')
                ->table('seguranca.user_locations')
                ->where('user_id', $user->id)
                ->delete();

            // Deletar usuário diretamente do banco
            // A tabela users está no schema seguranca, mas o Eloquent já sabe disso
            $user->delete();

            DB::connection('cerberus')->commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            DB::connection('cerberus')->rollBack();
            Log::error('Erro ao excluir usuário: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao excluir usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Verificar se o usuário pertence ao tenant atual
            $this->checkUserTenantAccess($user->id);

            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();

            $statusText = $user->status == 1 ? 'ativado' : 'desativado';

            return redirect()->route('users.index')
                ->with('success', "Usuário {$statusText} com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao alterar status do usuário: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao alterar status do usuário']);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, string $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        try {
            $user = User::findOrFail($id);

            // Verificar se o usuário pertence ao tenant atual
            $this->checkUserTenantAccess($user->id);

            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('users.index')
                ->with('success', 'Senha alterada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao alterar senha: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Erro ao alterar senha']);
        }
    }

    /**
     * Verifica se o usuário pertence ao tenant atual
     */
    private function checkUserTenantAccess($userId)
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

        if (empty($locationIds)) {
            abort(403, 'Acesso negado. Nenhuma localização disponível.');
        }

        // Verificar se o usuário tem vínculo com alguma location do tenant atual
        $hasAccess = DB::connection('cerberus')
            ->table('seguranca.user_locations')
            ->where('user_id', $userId)
            ->whereIn('location_id', $locationIds)
            ->where('status', 1)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Acesso negado. Este usuário não pertence ao seu tenant.');
        }
    }
}
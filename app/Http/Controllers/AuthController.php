<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Services\CerberusAuthService;

class AuthController extends Controller
{
    protected $cerberusAuthService;

    public function __construct(CerberusAuthService $cerberusAuthService)
    {
        $this->cerberusAuthService = $cerberusAuthService;
    }
    /**
     * Exibe o formulário de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Processa o login usando API do Cerberus
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Autenticar com o Cerberus via API
            $authResult = $this->cerberusAuthService->authenticate(
                $request->email,
                $request->password
            );

            if (!$authResult['success']) {
                return back()->withErrors([
                    'email' => $authResult['message'] ?? 'Credenciais inválidas'
                ])->withInput($request->only('email'));
            }

            $dados = $authResult['data'];

            // LOG: Verificar o que veio do Cerberus
            Log::info('=== DADOS RETORNADOS DO CERBERUS ===', [
                'items_count' => count($dados['items'] ?? []),
                'items_raw' => $dados['items'] ?? [],
                'perfis_count' => count($dados['perfis'] ?? []),
                'perfis' => $dados['perfis'] ?? [],
                'user' => $dados['user'] ?? null,
            ]);

            // Buscar dados completos do usuário do Cerberus (incluindo tenant e location)
            $userInfoResponse = Http::timeout(10)
                ->withOptions([
                    'verify' => false
                ])
                ->post(config('cerberus.url') . '/api/user/info', [
                    'email' => $request->email,
                    'system_key' => config('cerberus.system_key'),
                ]);

            $userData = null;
            $tenantId = null;
            $locationId = null;
            $tenantData = null;
            $locationData = null;
            $userLocations = [];
            $needsLocationSelection = false;

            if ($userInfoResponse->successful()) {
                $userInfoData = $userInfoResponse->json();

                if ($userInfoData['success'] && isset($userInfoData['data']['user'])) {
                    $userData = $userInfoData['data']['user'];

                    // Extrair dados do tenant e location
                    if (isset($userData['user_locations']) && !empty($userData['user_locations'])) {
                        $userLocations = $userData['user_locations'];

                        // Se o usuário tem apenas 1 location, selecionar automaticamente
                        if (count($userLocations) === 1) {
                            $userLocation = $userLocations[0];
                            $tenantId = $userLocation['tenant_id'] ?? null;
                            $locationId = $userLocation['location_id'] ?? null;

                            // Armazenar dados completos do tenant e location (vêm da API)
                            if (isset($userLocation['tenant'])) {
                                $tenantData = (object)$userLocation['tenant'];
                            }
                            if (isset($userLocation['location'])) {
                                $locationData = (object)$userLocation['location'];
                            }
                        } else {
                            // Se tem múltiplas locations, marcar que precisa escolher
                            $needsLocationSelection = true;
                            // Pegar dados da primeira location (todas devem ter o mesmo tenant)
                            if (isset($userLocations[0]['tenant'])) {
                                $tenantData = (object)$userLocations[0]['tenant'];
                                $tenantId = $userLocations[0]['tenant_id'] ?? null;
                            }
                        }
                    }
                }
            }

            // Buscar usuário local para fazer login no Laravel
            $user = User::where('email', $request->email)
                ->where('status', 1)
                ->first();

            if (!$user) {
                return back()->withErrors([
                    'email' => 'Usuário não encontrado no sistema.',
                ])->withInput($request->only('email'));
            }

            // Fazer login
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            // Armazenar dados na sessão
            session([
                'cerberusToken' => $dados['token'],
                'perfis' => $dados['perfis'] ?? [],
                'items' => $dados['items'] ?? [], // Itens de menu/permissões do Cerberus
                'user' => (object)($dados['user'] ?? null),
                'user_locations' => $userLocations,
                'needs_location_selection' => $needsLocationSelection,
                'location_id' => $locationId,
                'location' => $locationData,
                'tenant_id' => $tenantId,
                'tenant' => $tenantData, // Dados do tenant vêm da API
            ]);

            // LOG: Verificar o que foi salvo na sessão
            Log::info('=== DADOS SALVOS NA SESSÃO ===', [
                'items_count' => count(session('items', [])),
                'items_sample' => array_slice(session('items', []), 0, 2), // Primeiros 2 items
                'perfis_count' => count(session('perfis', [])),
                'has_token' => !empty(session('cerberusToken')),
            ]);

            // Cache local do usuário
            Cache::put("visaosis_user_{$user->id}", $user, 3600);

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            Log::error('Erro ao fazer login: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Ocorreu um erro ao processar o login. Tente novamente.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Busca informações do usuário por email via API do Cerberus
     */
    public function getUserInfo(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            // Buscar informações do usuário no Cerberus via API
            $response = Http::timeout(10)
                ->withOptions([
                    'verify' => false
                ])
                ->post(config('cerberus.url') . '/api/user/info', [
                    'email' => $request->email,
                    'system_key' => config('cerberus.system_key'),
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['success']) && $data['success'] && isset($data['data']['user'])) {
                    $user = $data['data']['user'];

                    // Extrair informações de tenant e location dos dados já retornados
                    $tenantInfo = null;
                    $locationInfo = null;

                    if (isset($user['user_locations']) && !empty($user['user_locations'])) {
                        $userLocation = $user['user_locations'][0]; // Pegar a primeira location

                        // Os dados do tenant e location já vêm incluídos
                        if (isset($userLocation['tenant'])) {
                            $tenantInfo = [
                                'name' => $userLocation['tenant']['name'] ?? null,
                                'trade_name' => $userLocation['tenant']['trade_name'] ?? null,
                            ];
                        }

                        if (isset($userLocation['location'])) {
                            $locationInfo = [
                                'name' => $userLocation['location']['name'] ?? null,
                                'short_name' => $userLocation['location']['short_name'] ?? null
                            ];
                        }
                    }

                    $userData = [
                        'name' => $user['name'] ?? 'Nome não informado',
                        'email' => $user['email'] ?? $request->email,
                        'role' => 'Administrador',
                        'position' => 'Administrador',
                        'tenant_id' => $tenantInfo ? ($user['user_locations'][0]['tenant_id'] ?? null) : null,
                        'location_id' => $locationInfo ? ($user['user_locations'][0]['location_id'] ?? null) : null,
                        'tenant' => $tenantInfo,
                        'location' => $locationInfo,
                        'photo' => null
                    ];

                    return response()->json([
                        'success' => true,
                        'user' => $userData
                    ]);
                }
            }

            // Se não encontrou no Cerberus, retornar dados mockados para teste
            return response()->json([
                'success' => true,
                'user' => [
                    'name' => 'Usuário Teste',
                    'email' => $request->email,
                    'role' => 'Usuário',
                    'position' => 'Funcionário',
                    'tenant' => [
                        'name' => 'Empresa Teste',
                        'trade_name' => 'Empresa Teste Ltda'
                    ],
                    'location' => [
                        'name' => 'Localização Teste',
                        'short_name' => 'Teste'
                    ],
                    'photo' => null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar informações do usuário: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar informações do usuário'
            ]);
        }
    }

    /**
     * Seleciona uma location para o usuário
     */
    public function selectLocation(Request $request)
    {
        $request->validate([
            'location_id' => 'required|integer'
        ]);

        $userLocations = session('user_locations', []);
        $locationId = $request->location_id;

        // Encontrar a location selecionada
        $selectedLocation = collect($userLocations)->firstWhere('location_id', $locationId);

        if (!$selectedLocation) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location não encontrada'
                ], 404);
            }
            return back()->withErrors(['location_id' => 'Location não encontrada']);
        }

        // Os dados do tenant já vêm incluídos no userLocation da API
        $tenantId = $selectedLocation['tenant_id'] ?? null;
        $tenant = null;
        
        // Se a location tem dados do tenant, usar eles (vêm da API)
        if (isset($selectedLocation['tenant'])) {
            $tenant = (object)$selectedLocation['tenant'];
        }

        // Atualizar sessão com a location selecionada
        session([
            'location_id' => $selectedLocation['location_id'],
            'location' => (object)$selectedLocation['location'],
            'needs_location_selection' => false,
            'tenant_id' => $tenantId,
            'tenant' => $tenant, // Dados do tenant vêm da API
        ]);

        // Se for requisição AJAX/JSON (do modal), retornar JSON
        // Verificar se é requisição AJAX ou se o Accept header pede JSON
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Location selecionada com sucesso',
                'location' => $selectedLocation['location']
            ]);
        }

        // Se for requisição de formulário (do dropdown), redirecionar
        return redirect()->back()->with('success', 'Localidade alterada com sucesso!');
    }

    /**
     * Processa o logout
     */
    public function logout(Request $request)
    {
        $token = Session::get('cerberusToken');

        if ($token) {
            // Revogar token no Cerberus
            $this->cerberusAuthService->revokeToken($token);
        }

        // Limpar sessão local
        $userId = Session::get('user.id');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            Cache::forget("visaosis_user_{$userId}");
        }

        return redirect()->route('login');
    }
}
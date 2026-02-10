<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CerberusAuthService
{
    private $cerberusUrl;
    private $systemKey;

    public function __construct()
    {
        $this->cerberusUrl = config('cerberus.url');
        $this->systemKey = config('cerberus.system_key');
    }

    /**
     * Autentica um usuário no Cerberus
     */
    public function authenticate(string $email, string $password)
    {
        try {
            Log::info('Tentando autenticar no Cerberus', [
                'email' => $email,
                'url' => $this->cerberusUrl . '/api/login',
                'system_key' => $this->systemKey
            ]);

            $response = Http::timeout(10)
                ->withOptions([
                    'verify' => false  // Desabilitar verificação SSL em desenvolvimento
                ])
                ->post($this->cerberusUrl . '/api/login', [
                    'email' => $email,
                    'password' => $password,
                    'system_key' => $this->systemKey,
                ]);

            Log::info('Resposta do Cerberus', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    $responseData = $data['data'];
                    
                    // Cache do token por 1 hora
                    Cache::put("cerberus_token_{$email}", $responseData['token'], 3600);
                    
                    return [
                        'success' => true,
                        'data' => [
                            'user' => $responseData['user'],
                            'token' => $responseData['token'],
                            'items' => $responseData['items'] ?? [],
                            'perfis' => $responseData['perfis'] ?? []
                        ]
                    ];
                } else {
                    Log::warning('Cerberus retornou success=false', ['data' => $data]);
                }
            } else {
                Log::error('Resposta não bem-sucedida do Cerberus', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Erro na autenticação'
            ];

        } catch (\Exception $e) {
            Log::error('Erro na autenticação Cerberus: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro de conexão com o sistema de autenticação: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida um token com o Cerberus
     */
    public function validateToken(string $token)
    {
        try {
            $response = Http::timeout(10)
                ->withOptions([
                    'verify' => false
                ])
                ->post($this->cerberusUrl . '/api/auth/validate-token', [
                    'token' => $token,
                    'system_key' => $this->systemKey,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['success'] ? $data : ['success' => false];
            }

            return ['success' => false];

        } catch (\Exception $e) {
            Log::error('Erro na validação do token Cerberus: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Revoga um token no Cerberus
     */
    public function revokeToken(string $token)
    {
        try {
            $response = Http::timeout(10)
                ->withOptions([
                    'verify' => false
                ])
                ->post($this->cerberusUrl . '/api/auth/revoke-token', [
                    'token' => $token,
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Erro ao revogar token Cerberus: ' . $e->getMessage());
            return false;
        }
    }
}

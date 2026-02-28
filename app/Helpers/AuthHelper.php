<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * Obtém o usuário autenticado do Laravel Auth
     */
    public static function user()
    {
        return Auth::user();
    }

    /**
     * Obtém o ID do usuário autenticado
     */
    public static function id()
    {
        return Auth::id();
    }

    /**
     * Obtém o nome do usuário autenticado
     */
    public static function name()
    {
        $user = self::user();
        return $user ? $user->name : null;
    }

    /**
     * Obtém o email do usuário autenticado
     */
    public static function email()
    {
        $user = self::user();
        return $user ? $user->email : null;
    }

    /**
     * Obtém o token do Cerberus
     */
    public static function token()
    {
        return Session::get('cerberusToken');
    }

    /**
     * Obtém as permissões do usuário (items do Cerberus)
     */
    public static function permissions()
    {
        return Session::get('items', []);
    }

    /**
     * Obtém os itens de menu organizados em hierarquia
     * Alias para permissions() para compatibilidade
     */
    public static function items()
    {
        return self::permissions();
    }

    /**
     * Verifica se o usuário tem uma permissão específica (item/rota)
     * Busca recursivamente nos items e seus filhos
     */
    public static function hasPermission($permission)
    {
        $items = self::permissions();
        
        if (empty($items)) {
            return false;
        }

        return self::searchPermissionInItems($items, $permission);
    }

    /**
     * Busca recursivamente uma permissão nos items
     */
    private static function searchPermissionInItems($items, $permission)
    {
        foreach ($items as $item) {
            // Verificar se a URL ou item_key correspondem
            if (isset($item['url']) && self::matchesPermission($item['url'], $permission)) {
                return true;
            }
            
            if (isset($item['item_key']) && $item['item_key'] === $permission) {
                return true;
            }

            // Buscar nos filhos
            if (isset($item['children']) && is_array($item['children']) && !empty($item['children'])) {
                if (self::searchPermissionInItems($item['children'], $permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se uma URL/rota corresponde a uma permissão
     */
    private static function matchesPermission($url, $permission)
    {
        // Comparação direta
        if ($url === $permission || $url === '/' . ltrim($permission, '/')) {
            return true;
        }

        // Se a URL contém route(), extrair o nome da rota
        if (preg_match("/route\(['\"]?([^'\"]+)['\"]?\)/", $url, $matches)) {
            return $matches[1] === $permission;
        }

        return false;
    }

    /**
     * Verifica se o usuário está autenticado
     */
    public static function check()
    {
        return Auth::check() && Session::has('cerberusToken');
    }

    /**
     * Obtém os menus do usuário
     * 
     * @param string|null $tipo Tipo de menu: 'left_sidebar', 'topnav', etc. Se null, retorna todos
     * @return array
     */
    public static function menus($tipo = null)
    {
        $items = self::permissions();
        
        if (empty($items)) {
            return [];
        }

        // Se não especificou tipo, retornar todos os items
        if ($tipo === null) {
            return $items;
        }

        // Filtrar por tipo de menu usando o MenuHelper
        return \App\Helpers\MenuHelper::getMenuItems($tipo);
    }

    /**
     * Obtém os perfis do usuário
     */
    public static function profiles()
    {
        return Session::get('perfis', []);
    }

    /**
     * Obtém o tenant_id do usuário autenticado
     */
    public static function tenantId()
    {
        return Session::get('tenant_id');
    }

    /**
     * Obtém o location_id do usuário autenticado
     */
    public static function locationId()
    {
        return Session::get('location_id');
    }

    /**
     * Obtém dados completos do tenant
     */
    public static function tenant()
    {
        return Session::get('tenant');
    }

    /**
     * Obtém o nome do tenant
     */
    public static function tenantName()
    {
        $tenant = self::tenant();
        return $tenant ? ($tenant->name ?? $tenant->trade_name ?? null) : null;
    }

    /**
     * Obtém o logo_path do tenant
     */
    public static function tenantLogo()
    {
        $tenant = self::tenant();
        return $tenant && property_exists($tenant, 'logo_path') ? $tenant->logo_path : null;
    }

    /**
     * Obtém a URL completa do logo do tenant
     */
    public static function tenantLogoUrl()
    {
        $logoPath = self::tenantLogo();
        
        if (empty($logoPath)) {
            return null;
        }
        
        // Se já é uma URL completa, retorna como está
        if (filter_var($logoPath, FILTER_VALIDATE_URL)) {
            return $logoPath;
        }
        
        // Se é um caminho relativo, constrói a URL completa
        // Assumindo que o logo está no storage público do Cerberus
        $cerberusUrl = config('cerberus.url');
        $fullUrl = rtrim($cerberusUrl, '/') . '/storage/' . ltrim($logoPath, '/');
        
        return $fullUrl;
    }

    /**
     * Verifica se o tenant tem logo
     */
    public static function hasTenantLogo()
    {
        $logo = self::tenantLogo();
        return !empty($logo) && is_string($logo) && trim($logo) !== '';
    }

    /**
     * Obtém dados completos da location
     */
    public static function location()
    {
        return Session::get('location');
    }

    /**
     * Obtém o nome da location
     */
    public static function locationName()
    {
        $location = self::location();
        return $location ? ($location->name ?? $location->short_name ?? null) : null;
    }

    /**
     * Obtém todas as locations do usuário autenticado
     */
    public static function userLocations()
    {
        return Session::get('user_locations', []);
    }

    /**
     * Verifica se usuário precisa selecionar uma location
     */
    public static function needsLocationSelection()
    {
        return Session::get('needs_location_selection', false);
    }

    /**
     * Obtém o ID do usuário autenticado (alias para id())
     */
    public static function userId()
    {
        return self::id();
    }

    /**
     * Obtém dados completos do usuário autenticado
     */
    public static function userData()
    {
        return self::user();
    }

    /**
     * Verifica se usuário pode acessar uma funcionalidade específica
     * Mapeamento de funcionalidades para rotas/permissões
     */
    public static function canAccess($funcionalidade)
    {
        $permissoes = [
            'criar_usuario' => '/admin/users',
            'editar_usuario' => '/admin/users',
            'excluir_usuario' => '/admin/users',
            'criar_paciente' => '/patients/create',
            'editar_paciente' => '/patients/edit',
            'excluir_paciente' => '/patients/delete',
            'ver_financeiro' => '/financial',
            'gerenciar_produtos' => '/products',
            'ver_relatorios' => '/reports',
            'gerenciar_atendimentos' => '/attendance',
            'gerenciar_vendas' => '/sales',
            'ver_clientes' => '/clients',
            'ver_dashboard' => '/dashboard',
            'gerenciar_recepcao' => '/recepcao',
            'gerenciar_profissionais' => '/professional',
        ];
        
        if (!isset($permissoes[$funcionalidade])) {
            return false;
        }
        
        return self::hasPermission($permissoes[$funcionalidade]);
    }

    /**
     * Verifica se usuário tem qualquer uma das permissões listadas
     */
    public static function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se usuário tem todas as permissões listadas
     */
    public static function hasAllPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Obtém dados do usuário do Cerberus (armazenados na sessão)
     */
    public static function cerberusUser()
    {
        return Session::get('user');
    }

    /**
     * Obtém todas as informações de debug da sessão
     */
    public static function debugSession()
    {
        return [
            'auth_check' => Auth::check(),
            'user_id' => self::id(),
            'user_name' => self::name(),
            'user_email' => self::email(),
            'has_token' => !empty(self::token()),
            'tenant_id' => self::tenantId(),
            'location_id' => self::locationId(),
            'total_items' => count(self::permissions()),
            'total_profiles' => count(self::profiles()),
            'total_locations' => count(self::userLocations()),
            'needs_location' => self::needsLocationSelection(),
        ];
    }
}

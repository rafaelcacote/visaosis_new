<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
     * Verifica se o usuário logado pode conceder desconto sem autorização de supervisor.
     */
    public static function canApplyDiscountWithoutAuthorization(): bool
    {
        return self::sessionHasPrivilegedProfile();
    }

    /**
     * Verifica se um usuário (sessão ou modelo) possui perfil privilegiado para desconto.
     */
    public static function userHasPrivilegedProfile($user = null): bool
    {
        if ($user === null) {
            return self::sessionHasPrivilegedProfile();
        }

        $privilegedNames = config('sales.discount_privileged_profiles', []);
        if (empty($privilegedNames)) {
            return false;
        }

        try {
            $profileIds = \Illuminate\Support\Facades\DB::connection('cerberus')
                ->table('seguranca.user_profile')
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->pluck('profile_id');

            if ($profileIds->isEmpty()) {
                return false;
            }

            $profiles = \Illuminate\Support\Facades\DB::connection('cerberus')
                ->table('seguranca.profiles')
                ->whereIn('id', $profileIds)
                ->where('status', 1)
                ->get(['name', 'short_name']);
        } catch (\Exception $e) {
            return false;
        }

        foreach ($profiles as $profile) {
            if (self::profileMatchesPrivilegedList($profile->name ?? '', $profile->short_name ?? '', $privilegedNames)) {
                return true;
            }
        }

        return false;
    }

    private static function sessionHasPrivilegedProfile(): bool
    {
        $privilegedNames = config('sales.discount_privileged_profiles', []);

        foreach (self::profiles() as $profile) {
            $name = is_array($profile) ? ($profile['name'] ?? $profile['nome'] ?? '') : (string) $profile;
            $shortName = is_array($profile) ? ($profile['short_name'] ?? '') : '';

            if (self::profileMatchesPrivilegedList($name, $shortName, $privilegedNames)) {
                return true;
            }
        }

        return false;
    }

    private static function profileMatchesPrivilegedList(string $name, string $shortName, array $privilegedNames): bool
    {
        $candidates = array_filter([$name, $shortName]);

        foreach ($candidates as $candidate) {
            $normalized = mb_strtolower(trim($candidate));

            foreach ($privilegedNames as $allowed) {
                $allowedNormalized = mb_strtolower(trim((string) $allowed));

                if ($normalized === $allowedNormalized || str_contains($normalized, $allowedNormalized)) {
                    return true;
                }
            }
        }

        return false;
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
     * Obtém a logo do tenant já baixada, redimensionada e codificada em
     * base64, pronta para embutir em templates de PDF. Logos enviadas em
     * resolução muito alta (ex. milhares de pixels) estouram o limite de
     * memória do PHP quando o DomPDF tenta processá-las; aqui a imagem é
     * reduzida antes disso. Retorna null se não houver logo ou em caso de
     * falha ao baixar/processar a imagem.
     */
    public static function tenantLogoBase64(int $maxDimension = 200): ?string
    {
        static $requestCache = [];

        if (! self::hasTenantLogo()) {
            return null;
        }

        $logoUrl = self::tenantLogoUrl();
        if (! $logoUrl) {
            return null;
        }

        $cacheKey = $logoUrl . '|' . $maxDimension;
        if (array_key_exists($cacheKey, $requestCache)) {
            return $requestCache[$cacheKey];
        }

        // Baixar e redimensionar uma logo grande pode levar vários segundos
        // (decodificação de imagem em alta resolução); evitamos repetir
        // esse custo a cada PDF gerado guardando o resultado em cache por
        // algumas horas, chaveado pela URL da logo.
        $persistentCacheKey = 'tenant_logo_base64_' . md5($cacheKey);

        $result = Cache::remember($persistentCacheKey, now()->addHours(12), function () use ($logoUrl, $maxDimension) {
            // Decodificar e redimensionar uma logo em altíssima resolução
            // pode levar mais tempo do que o limite padrão de execução do
            // PHP em ambiente web; como isso só roda uma vez a cada ciclo
            // de cache, estendemos o limite apenas para esta operação.
            @set_time_limit(90);

            try {
                $context = stream_context_create([
                    'http' => ['timeout' => 15],
                    'https' => ['timeout' => 15],
                ]);
                $imageData = @file_get_contents($logoUrl, false, $context);

                if ($imageData === false || $imageData === '') {
                    return '';
                }

                return self::resizeImageForPdf($imageData, $maxDimension) ?? '';
            } catch (\Throwable $e) {
                return '';
            }
        });

        // Cache não armazena null; usamos string vazia como marcador de falha.
        return $requestCache[$cacheKey] = ($result !== '' ? $result : null);
    }

    /**
     * Redimensiona uma imagem (em memória) para no máximo $maxDimension
     * pixels de largura/altura, preservando transparência, e retorna a
     * string "data:image/png;base64,...". Retorna null se o GD não estiver
     * disponível ou a imagem não puder ser lida.
     */
    private static function resizeImageForPdf(string $imageData, int $maxDimension): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        // Usa getimagesize (leve, não decodifica a imagem inteira) para
        // estimar quanta memória o GD vai precisar para decodificar a
        // imagem original e calcular um limite temporário suficiente,
        // evitando tanto o estouro de memória quanto um limite arbitrário
        // excessivo.
        $imageInfo = @getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            return null;
        }
        [$width, $height] = $imageInfo;

        $estimatedBytes = $width * $height * 4 * 3 + (10 * 1024 * 1024);
        $requiredLimitMb = max(256, (int) ceil($estimatedBytes / (1024 * 1024)));
        $requiredLimitMb = min($requiredLimitMb, 2048); // teto de segurança

        // Imagens muito grandes exigem mais memória do que o limite padrão
        // do PHP para serem decodificadas pelo GD; aumentamos temporariamente
        // o limite apenas para este processamento pontual. Não revertemos
        // para o valor anterior ao final: o PHP recusa reduzir o limite
        // abaixo do uso de memória corrente (lança erro), e cada requisição
        // web tem seu próprio processo/worker que é reciclado depois.
        $requiredLimit = ini_get('memory_limit');
        if ($requiredLimit !== '-1') {
            ini_set('memory_limit', $requiredLimitMb . 'M');
        }

        $source = @imagecreatefromstring($imageData);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        $ratio = min(1, $maxDimension / max($width, $height, 1));
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $destination = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 0, 0, 0, 127);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagepng($destination);
        $output = ob_get_clean();

        imagedestroy($source);
        imagedestroy($destination);

        return $output ? ('data:image/png;base64,' . base64_encode($output)) : null;
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

    /**
     * Obtém os dados PIX do tenant a partir da conexão Cerberus (seguranca.dados_pix).
     * Retorna null se tenant não identificado ou registro não existir.
     *
     * @param int|null $tenantId
     * @return object{tipo_chave:string,chave:string,nome_titular:string,banco:string,updated_at:string|null,created_at:string|null}|null
     */
    public static function tenantPixData(?int $tenantId = null): ?object
    {
        $tenantId = $tenantId ?: (int) self::tenantId();
        if ($tenantId <= 0) {
            return null;
        }

        try {
            return DB::connection('cerberus')
                ->table('seguranca.dados_pix')
                ->where('tenant_id', $tenantId)
                ->first() ?: null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Obtém o nome comercial/razão social do tenant vindo da sessão (API Cerberus).
     * Prioridade: trade_name > name. Fallback null.
     */
    public static function tenantCompanyName(): ?string
    {
        $tenant = Session::get('tenant');
        if (is_object($tenant)) {
            $name = trim((string) ($tenant->trade_name ?? ($tenant->name ?? '')));
            if ($name !== '') {
                return $name;
            }
        }
        if (is_array($tenant)) {
            $name = trim((string) ($tenant['trade_name'] ?? ($tenant['name'] ?? '')));
            if ($name !== '') {
                return $name;
            }
        }
        return null;
    }
}

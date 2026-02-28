<?php

namespace App\Helpers;

class MenuHelper
{
    /**
     * Obtém os itens de menu da sessão, filtrados por tipo de menu
     * 
     * @param string $typeMenu Tipo de menu: 'left_sidebar', 'topnav', etc.
     * @return array
     */
    public static function getMenuItems($typeMenu = 'left_sidebar')
    {
        $items = session('items', []);
        
        if (empty($items) || !is_array($items)) {
            \Log::warning('MenuHelper: Nenhum item na sessão');
            return [];
        }

        \Log::info('MenuHelper: Processando items', [
            'total_items' => count($items),
            'type_menu_filter' => $typeMenu,
            'items_raw' => $items
        ]);

        // MODO FLEXÍVEL: Se type_menu não estiver definido, aceitar todos os items com show_menu = true
        $filteredItems = [];
        
        foreach ($items as $item) {
            if (!is_array($item)) {
                \Log::warning('MenuHelper: Item não é array', ['item' => $item]);
                continue;
            }
            
            // Verificar se deve aparecer no menu
            $showMenu = $item['show_menu'] ?? true;
            $itemTypeMenu = $item['type_menu'] ?? null;
            $hasChildren = !empty($item['children']) && is_array($item['children']);
            
            \Log::info('MenuHelper: Processando item', [
                'name' => $item['name'] ?? 'Sem nome',
                'show_menu' => $showMenu,
                'type_menu' => $itemTypeMenu,
                'has_children' => $hasChildren
            ]);
            
            // LÓGICA FLEXÍVEL PARA SIDEBAR:
            // Aceita TODOS os items com show_menu = true e is_menu = true
            // Filtra apenas os filhos por type_menu
            
            // Verificações básicas
            if ($showMenu === false || $showMenu === 0 || $showMenu === '0') {
                \Log::info('MenuHelper: Item pulado (show_menu = false)', ['item' => $item['name'] ?? 'Sem nome']);
                continue;
            }
            
            // Se is_menu é false, pular (items que não devem aparecer no menu)
            if (isset($item['is_menu']) && ($item['is_menu'] === false || $item['is_menu'] === 0 || $item['is_menu'] === '0')) {
                \Log::info('MenuHelper: Item pulado (is_menu = false)', ['item' => $item['name'] ?? 'Sem nome']);
                continue;
            }
            
            // Se chegou aqui, o item deve aparecer no sidebar
            $shouldInclude = true;
            
            // Processar filhos (se existirem) - filtrar apenas os filhos por type_menu
            $filteredChildren = [];
            if ($hasChildren) {
                foreach ($item['children'] as $child) {
                    if (!is_array($child)) {
                        continue;
                    }
                    
                    $childShowMenu = $child['show_menu'] ?? true;
                    $childTypeMenu = $child['type_menu'] ?? null;
                    
                    // Pular se show_menu for false
                    if ($childShowMenu === false || $childShowMenu === 0 || $childShowMenu === '0') {
                        continue;
                    }
                    
                    // Para o sidebar, aceitar filhos que:
                    // 1. Não têm type_menu definido (null ou vazio)
                    // 2. Têm type_menu = "left_sidebar"
                    // 3. Têm type_menu = "topnav" (para compatibilidade - mostrar também)
                    // Basicamente, aceitar TODOS os filhos com show_menu = true
                    if ($childTypeMenu === null || $childTypeMenu === '' || 
                        $childTypeMenu === $typeMenu || 
                        $childTypeMenu === 'topnav') {
                        $filteredChildren[] = $child;
                        \Log::info('MenuHelper: Filho incluído', [
                            'parent' => $item['name'] ?? 'Sem nome',
                            'child' => $child['name'] ?? 'Sem nome',
                            'child_type_menu' => $childTypeMenu
                        ]);
                    }
                }
                
                \Log::info('MenuHelper: Item incluído', [
                    'item' => $item['name'] ?? 'Sem nome',
                    'type_menu' => $itemTypeMenu,
                    'children_count' => count($filteredChildren)
                ]);
            } else {
                \Log::info('MenuHelper: Item incluído (sem filhos)', [
                    'item' => $item['name'] ?? 'Sem nome',
                    'type_menu' => $itemTypeMenu
                ]);
            }
            
            if ($shouldInclude) {
                $itemCopy = $item;
                if (!empty($filteredChildren)) {
                    $itemCopy['children'] = $filteredChildren;
                }
                $filteredItems[] = $itemCopy;
            }
        }

        \Log::info('MenuHelper: Resultado final', [
            'total_filtered' => count($filteredItems),
            'items' => array_map(function($item) {
                return [
                    'name' => $item['name'] ?? 'Sem nome',
                    'has_children' => !empty($item['children']),
                    'children_count' => isset($item['children']) ? count($item['children']) : 0
                ];
            }, $filteredItems)
        ]);

        // Ordenar por ordering
        usort($filteredItems, function ($a, $b) {
            $orderA = $a['ordering'] ?? 999;
            $orderB = $b['ordering'] ?? 999;
            return $orderA <=> $orderB;
        });

        return $filteredItems;
    }

    /**
     * Verifica se o usuário tem permissão para acessar uma rota
     * 
     * @param string $route Nome da rota ou URL
     * @return bool
     */
    public static function hasPermission($route)
    {
        $items = session('items', []);
        
        if (empty($items)) {
            return false;
        }

        // Buscar recursivamente nos items e seus filhos
        return self::searchInItems($items, $route);
    }

    /**
     * Busca recursivamente um item por rota/URL
     * 
     * @param array $items
     * @param string $route
     * @return bool
     */
    private static function searchInItems($items, $route)
    {
        foreach ($items as $item) {
            // Verificar se a URL do item corresponde à rota
            if (isset($item['url']) && self::matchesRoute($item['url'], $route)) {
                return true;
            }

            // Buscar nos filhos
            if (isset($item['children']) && is_array($item['children']) && !empty($item['children'])) {
                if (self::searchInItems($item['children'], $route)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se uma URL corresponde a uma rota
     * 
     * @param string $url
     * @param string $route
     * @return bool
     */
    private static function matchesRoute($url, $route)
    {
        // Se a URL começa com /, é uma rota nomeada ou path
        if (strpos($url, '/') === 0) {
            // Tentar verificar se é uma rota nomeada
            try {
                $routeUrl = route($route, [], false);
                return $url === $routeUrl || $url === '/' . ltrim($routeUrl, '/');
            } catch (\Exception $e) {
                // Se não for rota nomeada, comparar diretamente
                return $url === $route || $url === '/' . ltrim($route, '/');
            }
        }

        // Se a URL contém route(), extrair o nome da rota
        if (preg_match("/route\(['\"]?([^'\"]+)['\"]?\)/", $url, $matches)) {
            return $matches[1] === $route;
        }

        return false;
    }

    /**
     * Renderiza um item de menu recursivamente
     * 
     * @param array $item
     * @param int $level Nível de profundidade (para indentação)
     * @return string
     */
    public static function renderMenuItem($item, $level = 0)
    {
        if (!isset($item['show_menu']) || !$item['show_menu']) {
            return '';
        }

        $hasChildren = !empty($item['children']) && is_array($item['children']);
        $icon = $item['icon'] ?? 'mdi mdi-circle';
        $name = $item['name'] ?? $item['short_name'] ?? 'Item';
        $url = $item['url'] ?? '#';
        $target = $item['target'] ?? '_self';
        $itemKey = $item['item_key'] ?? 'item_' . ($item['id'] ?? uniqid());
        
        // Se tem filhos, renderizar como submenu
        if ($hasChildren) {
            return self::renderSubmenu($item, $level);
        }

        // Item simples sem filhos
        return self::renderSimpleItem($item, $level);
    }

    /**
     * Renderiza um item simples (sem filhos)
     */
    private static function renderSimpleItem($item, $level)
    {
        $icon = $item['icon'] ?? 'mdi mdi-circle';
        $name = $item['name'] ?? $item['short_name'] ?? 'Item';
        $url = self::processUrl($item['url'] ?? '#');
        $target = $item['target'] ?? '_self';
        $isActive = self::isRouteActive($url);

        $html = '<li class="nav-item">';
        $html .= '<a class="nav-link ' . ($isActive ? 'active' : '') . '" href="' . htmlspecialchars($url) . '" target="' . htmlspecialchars($target) . '">';
        $html .= '<span class="icon-bg"><i class="' . htmlspecialchars($icon) . ' menu-icon"></i></span>';
        $html .= '<span class="menu-title">' . htmlspecialchars($name) . '</span>';
        $html .= '</a>';
        $html .= '</li>';

        return $html;
    }

    /**
     * Renderiza um submenu (com filhos)
     */
    private static function renderSubmenu($item, $level)
    {
        $icon = $item['icon'] ?? 'mdi mdi-circle';
        $name = $item['name'] ?? $item['short_name'] ?? 'Menu';
        $itemKey = 'menu_' . ($item['id'] ?? uniqid());
        $hasActiveChild = self::hasActiveChild($item['children'] ?? []);
        $isExpanded = $hasActiveChild ? 'true' : 'false';
        $showClass = $hasActiveChild ? 'show' : '';

        $html = '<li class="nav-item">';
        $html .= '<a class="nav-link" data-bs-toggle="collapse" href="#' . htmlspecialchars($itemKey) . '" aria-expanded="' . $isExpanded . '" aria-controls="' . htmlspecialchars($itemKey) . '">';
        $html .= '<span class="icon-bg"><i class="' . htmlspecialchars($icon) . ' menu-icon"></i></span>';
        $html .= '<span class="menu-title">' . htmlspecialchars($name) . '</span>';
        $html .= '<i class="menu-arrow"></i>';
        $html .= '</a>';
        $html .= '<div class="collapse ' . $showClass . '" id="' . htmlspecialchars($itemKey) . '">';
        $html .= '<ul class="nav flex-column sub-menu">';
        
        // Ordenar filhos por ordering
        $children = $item['children'] ?? [];
        usort($children, function ($a, $b) {
            $orderA = $a['ordering'] ?? 999;
            $orderB = $b['ordering'] ?? 999;
            return $orderA <=> $orderB;
        });

        foreach ($children as $child) {
            if (isset($child['show_menu']) && $child['show_menu']) {
                $html .= self::renderSubmenuItem($child);
            }
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    /**
     * Renderiza um item de submenu
     */
    private static function renderSubmenuItem($item)
    {
        $icon = $item['icon'] ?? 'mdi mdi-circle';
        $name = $item['name'] ?? $item['short_name'] ?? 'Item';
        $url = self::processUrl($item['url'] ?? '#');
        $target = $item['target'] ?? '_self';
        $isActive = self::isRouteActive($url);

        $html = '<li class="nav-item">';
        $html .= '<a class="nav-link ' . ($isActive ? 'active' : '') . '" href="' . htmlspecialchars($url) . '" target="' . htmlspecialchars($target) . '">';
        $html .= '<i class="' . htmlspecialchars($icon) . ' menu-icon me-2"></i>';
        $html .= htmlspecialchars($name);
        $html .= '</a>';
        $html .= '</li>';

        return $html;
    }

    /**
     * Verifica se algum filho está ativo
     */
    private static function hasActiveChild($children)
    {
        foreach ($children as $child) {
            if (isset($child['url'])) {
                $url = self::processUrl($child['url']);
                if (self::isRouteActive($url)) {
                    return true;
                }
            }
            if (!empty($child['children'])) {
                if (self::hasActiveChild($child['children'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Resolve URL quando o item tem url vazia ou "#" (bug: href="#" herda URL atual ao navegar)
     * Usa item_key para inferir o path correto (ex: dashboard -> /dashboard, sales.index -> /sales)
     */
    public static function resolveUrlFromItemKey(array $item): string
    {
        $key = $item['item_key'] ?? $item['name'] ?? null;
        if (empty($key) || !is_string($key)) {
            return '#';
        }
        $base = explode('.', $key)[0];
        if (empty($base)) {
            return '#';
        }
        return '/' . ltrim($base, '/');
    }

    /**
     * Processa a URL, convertendo route() para URL real
     */
    public static function processUrl($url)
    {
        if (empty($url) || $url === '#') {
            return '#';
        }

        // Se contém route(), tentar converter
        if (preg_match("/route\(['\"]?([^'\"]+)['\"]?\)/", $url, $matches)) {
            try {
                return route($matches[1]);
            } catch (\Exception $e) {
                return '#';
            }
        }

        // URLs externas (http, https, //, mailto, etc.) permanecem como estão
        if (preg_match('/^(https?:\/\/|\/\/|mailto:|tel:)/i', $url)) {
            return $url;
        }

        // Se já começa com /, é um path absoluto da aplicação
        if (strpos($url, '/') === 0) {
            return $url;
        }

        // Qualquer outra coisa tratamos como path interno relativo
        // e garantimos que comece com / para não \"herdar\" o prefixo da URL atual
        return '/' . ltrim($url, '/');
    }

    /**
     * Verifica se uma rota está ativa
     */
    public static function isRouteActive($url)
    {
        if (empty($url) || $url === '#') {
            return false;
        }

        $currentUrl = request()->url();
        $currentPath = request()->path();
        $currentRoute = request()->route() ? request()->route()->getName() : null;

        // Verificar se a URL atual corresponde
        if ($url === $currentUrl || $url === '/' . $currentPath) {
            return true;
        }

        // Se a URL contém route(), verificar pelo nome da rota
        if (preg_match("/route\(['\"]?([^'\"]+)['\"]?\)/", $url, $matches)) {
            $routeName = $matches[1];
            // Remover parâmetros da rota (ex: users.index -> users.*)
            $routePattern = str_replace('.index', '.*', $routeName);
            $routePattern = str_replace('.show', '.*', $routePattern);
            $routePattern = str_replace('.create', '.*', $routePattern);
            $routePattern = str_replace('.edit', '.*', $routePattern);
            
            if ($currentRoute && (
                $currentRoute === $routeName || 
                fnmatch($routePattern, $currentRoute)
            )) {
                return true;
            }
        }

        return false;
    }
}

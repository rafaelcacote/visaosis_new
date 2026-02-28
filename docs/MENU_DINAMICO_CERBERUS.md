# Sistema de Menu Dinâmico com Cerberus

Este documento descreve como funciona o sistema de menu dinâmico baseado nas permissões do Cerberus no VisaoSis.

## Visão Geral

O sistema integra o VisaoSis com o Cerberus (sistema de autenticação centralizado) para:
- Autenticar usuários
- Controlar permissões de acesso
- Montar menus dinâmicos baseados no perfil do usuário
- Controlar acesso a rotas específicas

## Componentes Implementados

### 1. AuthHelper (`app/Helpers/AuthHelper.php`)

Helper para facilitar o acesso aos dados do usuário e permissões do Cerberus.

**Exemplos de uso:**

```php
use App\Helpers\AuthHelper;

// Verificar se usuário está autenticado
if (AuthHelper::check()) {
    // Usuário autenticado
}

// Obter dados do usuário
$userId = AuthHelper::id();
$userName = AuthHelper::name();
$userEmail = AuthHelper::email();

// Obter dados do tenant
$tenantId = AuthHelper::tenantId();
$tenantName = AuthHelper::tenantName();
$tenantLogo = AuthHelper::tenantLogoUrl();

// Verificar permissões
if (AuthHelper::hasPermission('/dashboard')) {
    // Usuário tem acesso ao dashboard
}

// Verificar múltiplas permissões
if (AuthHelper::hasAnyPermission(['/patients', '/patients/create'])) {
    // Usuário tem acesso a pelo menos uma das rotas
}

// Verificar funcionalidades específicas
if (AuthHelper::canAccess('criar_paciente')) {
    // Usuário pode criar pacientes
}

// Obter items/permissões do Cerberus
$items = AuthHelper::permissions(); // ou AuthHelper::items()

// Obter menus filtrados por tipo
$sidebarMenus = AuthHelper::menus('left_sidebar');
$topnavMenus = AuthHelper::menus('topnav');
```

### 2. MenuHelper (`app/Helpers/MenuHelper.php`)

Helper para processar e filtrar items de menu do Cerberus.

**Exemplos de uso:**

```php
use App\Helpers\MenuHelper;

// Obter itens de menu do sidebar
$menuItems = MenuHelper::getMenuItems('left_sidebar');

// Obter itens de menu do topnav
$topnavItems = MenuHelper::getMenuItems('topnav');

// Verificar permissão
if (MenuHelper::hasPermission('/dashboard')) {
    // Tem permissão
}

// Processar URL (converte route() para URL real)
$url = MenuHelper::processUrl("route('dashboard')"); // Retorna: /dashboard

// Verificar se rota está ativa
if (MenuHelper::isRouteActive('/dashboard')) {
    // Rota atual é /dashboard
}
```

### 3. CheckCerberusPermission Middleware

Middleware para proteger rotas baseado nas permissões do Cerberus.

**Como usar nas rotas:**

```php
// Em routes/web.php

// Proteger uma rota com permissão específica
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'cerberus.permission:/dashboard']);

// Proteger com múltiplas permissões (OR - precisa de pelo menos uma)
Route::get('/patients', [PatientController::class, 'index'])
    ->middleware(['auth', 'cerberus.permission:/patients,/patients/view']);

// Proteger grupo de rotas
Route::middleware(['auth', 'cerberus.permission:/admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('/admin/config', [ConfigController::class, 'index']);
});

// Proteger apenas pela autenticação (sem permissão específica)
Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware(['auth', 'cerberus.permission']);
```

### 4. Sidebar Dinâmico

O componente sidebar (`resources/views/components/sidebar.blade.php`) renderiza automaticamente o menu baseado nas permissões do usuário.

**Como usar:**

```blade
{{-- Em um layout, incluir o sidebar --}}
<x-sidebar />

{{-- O sidebar automaticamente: --}}
{{-- 1. Obtém os items do Cerberus da sessão --}}
{{-- 2. Filtra por tipo de menu (left_sidebar) --}}
{{-- 3. Renderiza apenas items que o usuário tem acesso --}}
{{-- 4. Mostra logo do tenant --}}
{{-- 5. Destaca rota ativa --}}
```

### 5. Estrutura dos Items do Cerberus

Os items são retornados pelo Cerberus organizados em hierarquia:

```json
[
  {
    "id": 1,
    "name": "Dashboard",
    "short_name": "Dashboard",
    "url": "/dashboard",
    "icon": "mdi mdi-view-dashboard",
    "show_menu": true,
    "is_menu": true,
    "type_menu": "left_sidebar",
    "ordering": 1,
    "target": "_self",
    "item_key": "dashboard",
    "children": []
  },
  {
    "id": 2,
    "name": "Pacientes",
    "short_name": "Pacientes",
    "url": "/patients",
    "icon": "mdi mdi-account-multiple",
    "show_menu": true,
    "is_menu": true,
    "type_menu": "left_sidebar",
    "ordering": 2,
    "target": "_self",
    "item_key": "patients",
    "children": [
      {
        "id": 3,
        "name": "Listar Pacientes",
        "short_name": "Listar",
        "url": "/patients",
        "icon": "mdi mdi-format-list-bulleted",
        "show_menu": true,
        "is_menu": true,
        "type_menu": "left_sidebar",
        "ordering": 1,
        "target": "_self",
        "item_key": "patients.index"
      },
      {
        "id": 4,
        "name": "Novo Paciente",
        "short_name": "Novo",
        "url": "/patients/create",
        "icon": "mdi mdi-plus-circle",
        "show_menu": true,
        "is_menu": true,
        "type_menu": "left_sidebar",
        "ordering": 2,
        "target": "_self",
        "item_key": "patients.create"
      }
    ]
  }
]
```

## Usando em Blade Templates

### Verificar Permissões

```blade
{{-- Mostrar conteúdo baseado em permissão --}}
@if(App\Helpers\AuthHelper::hasPermission('/patients/create'))
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        Novo Paciente
    </a>
@endif

{{-- Mostrar conteúdo baseado em funcionalidade --}}
@if(App\Helpers\AuthHelper::canAccess('ver_financeiro'))
    <div class="financial-widget">
        {{-- Conteúdo financeiro --}}
    </div>
@endif
```

### Exibir Dados do Tenant/Location

```blade
{{-- Logo do tenant --}}
@if(App\Helpers\AuthHelper::hasTenantLogo())
    <img src="{{ App\Helpers\AuthHelper::tenantLogoUrl() }}" 
         alt="{{ App\Helpers\AuthHelper::tenantName() }}">
@endif

{{-- Nome do tenant --}}
<h1>{{ App\Helpers\AuthHelper::tenantName() }}</h1>

{{-- Localização --}}
<p>{{ App\Helpers\AuthHelper::locationName() }}</p>
```

## Fluxo de Autenticação

1. **Usuário faz login** → `AuthController::login()`
2. **Sistema autentica no Cerberus** → `CerberusAuthService::authenticate()`
3. **Cerberus valida e retorna:**
   - Token de acesso
   - Dados do usuário
   - Items/permissões organizados em hierarquia
   - Perfis do usuário
4. **Sistema armazena na sessão:**
   - `cerberusToken` - Token para validações futuras
   - `items` - Items/permissões do Cerberus
   - `perfis` - Perfis do usuário
   - `user` - Dados do usuário
   - `tenant_id`, `tenant` - Dados do tenant
   - `location_id`, `location` - Dados da localização
5. **Menu é renderizado** → Apenas items que o usuário tem acesso
6. **Rotas são protegidas** → Middleware valida acesso

## Configuração no Cerberus

### Cadastrar Sistema

No Cerberus, o sistema VisaoSis deve estar cadastrado com:
- **System Key:** `visaosis`
- **Nome:** VisaoSis
- **Status:** Ativo

### Criar Items/Menus

No Cerberus, criar items para cada funcionalidade:

```
Dashboard
├── URL: /dashboard
├── Icon: mdi mdi-view-dashboard
├── Type Menu: left_sidebar
└── Show Menu: true

Pacientes
├── URL: /patients
├── Icon: mdi mdi-account-multiple
├── Type Menu: left_sidebar
├── Show Menu: true
└── Filhos:
    ├── Listar Pacientes (/patients)
    └── Novo Paciente (/patients/create)

Recepção
├── URL: /recepcao
├── Icon: mdi mdi-desk
├── Type Menu: left_sidebar
├── Show Menu: true
└── Filhos:
    ├── Fila de Atendimento (/recepcao)
    └── Nova Triagem (/recepcao/triage)
```

### Atribuir Permissões

1. Criar perfis (ex: Médico, Recepcionista, Administrador)
2. Atribuir items aos perfis
3. Vincular usuários aos perfis

## Tipos de Menu

O sistema suporta diferentes tipos de menu:

- `left_sidebar` - Menu lateral esquerdo (padrão)
- `topnav` - Menu superior/navbar
- `right_sidebar` - Menu lateral direito
- Outros tipos personalizados

## Variáveis de Ambiente

```env
CERBERUS_URL=http://localhost:8000
CERBERUS_SYSTEM_KEY=visaosis
CERBERUS_TIMEOUT=10
CERBERUS_CACHE_DURATION=3600
```

## Troubleshooting

### Menu não aparece

1. Verificar se usuário está autenticado
2. Verificar se items foram salvos na sessão
3. Verificar se items têm `show_menu = true`
4. Verificar se `type_menu` está correto

**Debug:**
```php
dd(session('items')); // Ver items na sessão
dd(AuthHelper::permissions()); // Ver permissões
dd(MenuHelper::getMenuItems('left_sidebar')); // Ver menu processado
```

### Permissão negada

1. Verificar se usuário tem o item atribuído no Cerberus
2. Verificar se o perfil do usuário está ativo
3. Verificar se o item está ativo
4. Verificar middleware na rota

### Logo não aparece

1. Verificar se tenant tem logo cadastrado no Cerberus
2. Verificar se o caminho do logo está correto
3. Verificar configuração `CERBERUS_URL`

## Exemplo Completo

### Criar um novo módulo protegido

**1. Criar rota protegida:**
```php
// routes/web.php
Route::middleware(['auth', 'cerberus.permission:/inventory'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
});
```

**2. No Cerberus, criar items:**
```
Estoque (item pai)
- URL: /inventory
- Icon: mdi mdi-package-variant
- Type Menu: left_sidebar
- Show Menu: true
- Ordering: 5

  ├── Listar Estoque (filho)
  │   - URL: /inventory
  │   - Icon: mdi mdi-format-list-bulleted
  │   - Type Menu: left_sidebar
  │   - Show Menu: true
  
  └── Adicionar Produto (filho)
      - URL: /inventory/create
      - Icon: mdi mdi-plus-circle
      - Type Menu: left_sidebar
      - Show Menu: true
```

**3. Atribuir items ao perfil do usuário no Cerberus**

**4. No controller, usar permissões:**
```php
namespace App\Http\Controllers;

use App\Helpers\AuthHelper;

class InventoryController extends Controller
{
    public function index()
    {
        // Verificação adicional (opcional, pois já tem middleware)
        if (!AuthHelper::hasPermission('/inventory')) {
            abort(403);
        }
        
        return view('inventory.index');
    }
    
    public function create()
    {
        return view('inventory.create');
    }
}
```

**5. Na view, mostrar botões condicionais:**
```blade
{{-- resources/views/inventory/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Estoque</h1>
    
    @if(App\Helpers\AuthHelper::hasPermission('/inventory/create'))
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus"></i> Adicionar Produto
        </a>
    @endif
    
    {{-- Lista de produtos --}}
</div>
@endsection
```

Pronto! O menu aparecerá automaticamente no sidebar e as rotas estarão protegidas.

## Vantagens do Sistema

1. **Centralizado** - Permissões gerenciadas no Cerberus
2. **Dinâmico** - Menu muda conforme o perfil do usuário
3. **Seguro** - Middleware protege rotas automaticamente
4. **Flexível** - Suporta múltiplos tipos de menu e hierarquias
5. **Multi-tenant** - Suporta múltiplos tenants e localizações
6. **Manutenível** - Fácil adicionar novos módulos e permissões

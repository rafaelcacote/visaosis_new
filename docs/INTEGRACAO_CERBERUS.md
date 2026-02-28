# Integração com Cerberus - Guia Rápido

## O que é?

O Cerberus é um sistema de autenticação centralizado que gerencia:
- Usuários
- Perfis/Permissões
- Menus dinâmicos
- Multi-tenant
- Multi-location

## Como funciona?

1. Usuário faz login no VisaoSis
2. VisaoSis autentica com o Cerberus via API
3. Cerberus retorna token + permissões + dados do usuário
4. Menu é montado automaticamente baseado nas permissões
5. Rotas são protegidas pelo middleware

## Uso Rápido

### Verificar se usuário está autenticado
```php
use App\Helpers\AuthHelper;

if (AuthHelper::check()) {
    // Autenticado
}
```

### Obter dados do usuário
```php
$userName = AuthHelper::name();
$userEmail = AuthHelper::email();
$userId = AuthHelper::id();
```

### Verificar permissões
```php
if (AuthHelper::hasPermission('/dashboard')) {
    // Tem acesso ao dashboard
}
```

### Proteger rotas
```php
// routes/web.php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'cerberus.permission:/dashboard']);
```

### Em Blade Templates
```blade
@if(App\Helpers\AuthHelper::hasPermission('/patients/create'))
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        Novo Paciente
    </a>
@endif
```

## Configuração

### .env
```env
CERBERUS_URL=http://localhost:8000
CERBERUS_SYSTEM_KEY=visaosis
```

### No Cerberus

1. **Cadastrar o sistema VisaoSis**
   - System Key: `visaosis`
   - Nome: VisaoSis
   - Status: Ativo

2. **Criar Items (menus/funcionalidades)**
   - Dashboard → `/dashboard`
   - Pacientes → `/patients`
   - Recepção → `/recepcao`
   - etc.

3. **Criar Perfis**
   - Médico
   - Recepcionista
   - Administrador

4. **Atribuir Items aos Perfis**

5. **Vincular Usuários aos Perfis**

## Estrutura de Arquivos

```
app/
├── Helpers/
│   ├── AuthHelper.php      # Funções de autenticação
│   └── MenuHelper.php      # Funções de menu
├── Http/
│   └── Middleware/
│       └── CheckCerberusPermission.php  # Middleware de permissões
└── Services/
    └── CerberusAuthService.php  # Integração com API do Cerberus

resources/views/components/
└── sidebar.blade.php  # Componente do menu lateral

config/
└── cerberus.php  # Configurações do Cerberus

docs/
├── INTEGRACAO_CERBERUS.md  # Este arquivo
└── MENU_DINAMICO_CERBERUS.md  # Documentação completa
```

## Fluxo de Dados

```
Login
  ↓
AuthController::login()
  ↓
CerberusAuthService::authenticate()
  ↓
Cerberus API (/api/login)
  ↓
Retorna: Token + Items + Perfis + User
  ↓
Armazena na sessão
  ↓
MenuHelper::getMenuItems() filtra por tipo
  ↓
Sidebar renderiza menu
  ↓
Middleware protege rotas
```

## Helpers Disponíveis

### AuthHelper
- `AuthHelper::check()` - Verifica autenticação
- `AuthHelper::user()` - Dados do usuário (Laravel)
- `AuthHelper::name()` - Nome do usuário
- `AuthHelper::email()` - Email do usuário
- `AuthHelper::permissions()` - Items/permissões
- `AuthHelper::hasPermission($permission)` - Verifica permissão
- `AuthHelper::tenantName()` - Nome do tenant
- `AuthHelper::tenantLogoUrl()` - URL do logo
- `AuthHelper::locationName()` - Nome da localização

### MenuHelper
- `MenuHelper::getMenuItems($type)` - Itens de menu filtrados
- `MenuHelper::hasPermission($route)` - Verifica permissão
- `MenuHelper::processUrl($url)` - Processa URL (route() → URL)
- `MenuHelper::isRouteActive($url)` - Verifica se rota está ativa

## Exemplos Práticos

### Criar novo módulo

**1. Rota:**
```php
Route::middleware(['auth', 'cerberus.permission:/produtos'])->group(function () {
    Route::get('/produtos', [ProdutoController::class, 'index']);
});
```

**2. No Cerberus:**
- Criar item "Produtos" com URL `/produtos`
- Atribuir ao perfil desejado

**3. Pronto!** 
- Menu aparece automaticamente
- Rota está protegida

### Botão condicional
```blade
@if(App\Helpers\AuthHelper::hasPermission('/produtos/create'))
    <button>Novo Produto</button>
@endif
```

## Debug

```php
// Ver items na sessão
dd(session('items'));

// Ver menu processado
dd(MenuHelper::getMenuItems('left_sidebar'));

// Ver permissões
dd(AuthHelper::permissions());

// Debug completo da sessão
dd(AuthHelper::debugSession());
```

## Troubleshooting

| Problema | Solução |
|----------|---------|
| Menu não aparece | Verificar se items têm `show_menu = true` e `type_menu` correto |
| Permissão negada | Verificar se item está atribuído ao perfil no Cerberus |
| Logo não aparece | Verificar `CERBERUS_URL` e path do logo no tenant |
| Token inválido | Limpar sessão e fazer login novamente |

## Documentação Completa

Para documentação detalhada, veja: `docs/MENU_DINAMICO_CERBERUS.md`

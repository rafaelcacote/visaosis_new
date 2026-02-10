# Template ConnectPlus Admin - Integração Laravel

O template ConnectPlus Admin foi integrado ao projeto Laravel seguindo as melhores práticas do framework.

## Estrutura Criada

### Assets
- Todos os assets do template foram copiados para `public/assets/`
- Inclui: CSS, JS, imagens, fonts e vendors

### Views
- **Layout Principal**: `resources/views/layouts/app.blade.php`
  - Layout base que todas as páginas usarão
  - Inclui navbar, sidebar e footer

- **Componentes**:
  - `resources/views/components/navbar.blade.php` - Barra de navegação superior
  - `resources/views/components/sidebar.blade.php` - Menu lateral
  - `resources/views/components/footer.blade.php` - Rodapé

- **Dashboard**: `resources/views/dashboard.blade.php`
  - Página de exemplo usando o template

### Controllers
- `app/Http/Controllers/DashboardController.php` - Controller para o dashboard

### Rotas
- Rota `/dashboard` configurada em `routes/web.php`
- Rota `/` redireciona para `/dashboard`

## Como Usar

### Criar uma nova página

1. Crie uma nova view em `resources/views/`:

```blade
@extends('layouts.app')

@section('title', 'Minha Página')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Conteúdo da página</h1>
        </div>
    </div>
@endsection
```

2. Adicione a rota em `routes/web.php`:

```php
Route::get('/minha-pagina', [MeuController::class, 'index'])->name('minha-pagina');
```

### Adicionar CSS/JS específicos

Use os stacks `@push` no Blade:

```blade
@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/vendors/meu-plugin.css') }}">
@endpush

@push('plugin-js')
<script src="{{ asset('assets/vendors/meu-plugin.js') }}"></script>
@endpush

@push('scripts')
<script>
    // Seu código JavaScript
</script>
@endpush
```

### Personalizar Sidebar

Edite `resources/views/components/sidebar.blade.php` para adicionar/remover itens do menu.

### Personalizar Navbar

Edite `resources/views/components/navbar.blade.php` para modificar a barra superior.

## Assets Disponíveis

Todos os assets do template estão disponíveis em `public/assets/`:

- **CSS**: `public/assets/css/style.css`
- **JS**: `public/assets/js/`
- **Imagens**: `public/assets/images/`
- **Fonts**: `public/assets/fonts/`
- **Vendors**: `public/assets/vendors/` (Bootstrap, jQuery, Chart.js, etc.)

## Notas

- O template usa Bootstrap 5
- Material Design Icons estão incluídos
- Chart.js está disponível para gráficos
- Todos os caminhos de assets usam a função `asset()` do Laravel

## Próximos Passos

1. Personalizar o menu do sidebar conforme suas necessidades
2. Criar controllers e views para suas funcionalidades
3. Integrar com autenticação Laravel (já preparado para Auth::user())
4. Adicionar mais páginas conforme necessário
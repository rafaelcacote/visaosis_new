# Exemplo de Aplicação de Permissões nas Rotas

Este documento mostra como aplicar as permissões do Cerberus nas rotas existentes do VisaoSis.

## Estrutura Atual vs Estrutura com Permissões

### ANTES (apenas com auth)
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('pessoas', PessoaController::class);
});
```

### DEPOIS (com auth + permissões do Cerberus)
```php
Route::middleware(['auth'])->group(function () {
    // Dashboard - acessível a todos autenticados
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('cerberus.permission:/dashboard');
    
    // Pacientes - apenas quem tem permissão
    Route::middleware('cerberus.permission:/pessoas')->group(function () {
        Route::resource('pessoas', PessoaController::class);
    });
});
```

## Exemplo Completo: Arquivo web.php com Permissões

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RecepcaoController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\AttendanceController;

// ============================================
// Rotas Públicas (sem autenticação)
// ============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/api/user/info', [AuthController::class, 'getUserInfo']);

// ============================================
// Rotas Protegidas (com autenticação)
// ============================================
Route::middleware(['auth'])->group(function () {
    // Redirect raiz para dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // ========================================
    // Dashboard - Acesso geral
    // ========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('cerberus.permission:/dashboard');
    
    // ========================================
    // Seleção de Location (sem permissão específica)
    // ========================================
    Route::post('/location/select', [AuthController::class, 'selectLocation'])
        ->name('location.select');
    
    // ========================================
    // USUÁRIOS - Apenas Administradores
    // ========================================
    Route::middleware('cerberus.permission:/admin/users')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])
            ->name('users.change-password');
    });
    
    // ========================================
    // PROFISSIONAIS - Gestão de Profissionais
    // ========================================
    Route::middleware('cerberus.permission:/profissionais')->group(function () {
        Route::resource('profissionais', ProfissionalController::class)
            ->parameters(['profissionais' => 'profissional']);
        Route::get('profissionais/search', [ProfissionalController::class, 'search'])
            ->name('profissionais.search');
        Route::post('profissionais/{profissional}/toggle-status', [ProfissionalController::class, 'toggleStatus'])
            ->name('profissionais.toggle-status');
    });
    
    // ========================================
    // CATEGORIAS - Gestão de Categorias
    // ========================================
    Route::middleware('cerberus.permission:/categorias')->group(function () {
        Route::resource('categorias', CategoriaController::class)
            ->parameters(['categorias' => 'categoria']);
        Route::post('categorias/{categoria}/toggle-status', [CategoriaController::class, 'toggleStatus'])
            ->name('categorias.toggle-status');
    });

    // ========================================
    // LABORATÓRIOS - Gestão de Laboratórios
    // ========================================
    Route::middleware('cerberus.permission:/laboratorios')->group(function () {
        Route::resource('laboratorios', LaboratorioController::class)
            ->parameters(['laboratorios' => 'laboratorio']);
        Route::post('laboratorios/{laboratorio}/toggle-status', [LaboratorioController::class, 'toggleStatus'])
            ->name('laboratorios.toggle-status');
    });

    // ========================================
    // PRODUTOS - Gestão de Produtos
    // ========================================
    Route::middleware('cerberus.permission:/produtos')->group(function () {
        Route::resource('produtos', ProdutoController::class)
            ->parameters(['produtos' => 'produto']);
        Route::post('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])
            ->name('produtos.toggle-status');
    });

    // ========================================
    // PACIENTES (Pessoas) - Gestão de Pacientes
    // ========================================
    Route::middleware('cerberus.permission:/pessoas')->group(function () {
        Route::get('pessoas/search', [PessoaController::class, 'search'])
            ->name('pessoas.search');
        Route::resource('pessoas', PessoaController::class)
            ->parameters(['pessoas' => 'pessoa']);
        Route::post('pessoas/{pessoa}/toggle-status', [PessoaController::class, 'toggleStatus'])
            ->name('pessoas.toggle-status');
    });

    // ========================================
    // RECEPÇÃO - Módulo de Recepção
    // ========================================
    Route::middleware('cerberus.permission:/recepcao')->group(function () {
        Route::get('/recepcao', [RecepcaoController::class, 'index'])
            ->name('recepcao.index');
        
        Route::prefix('recepcao')->name('recepcao.')->group(function () {
            Route::get('/dashboard', [RecepcaoController::class, 'dashboard'])
                ->name('dashboard');
            Route::get('/triage', [RecepcaoController::class, 'triage'])
                ->name('triage');
            Route::post('/triage', [RecepcaoController::class, 'storeTriage'])
                ->name('triage.store');
            Route::get('/consulta/{consulta}', [RecepcaoController::class, 'show'])
                ->name('consulta.show');
            Route::post('/checkin', [RecepcaoController::class, 'checkin'])
                ->name('checkin');
            Route::patch('/status/{consulta}', [RecepcaoController::class, 'updateStatus'])
                ->name('updateStatus');
            Route::get('/api/patients/search', [RecepcaoController::class, 'searchPatient'])
                ->name('patients.search');
        });
    });

    // ========================================
    // ATENDIMENTO - Módulo de Atendimento
    // ========================================
    Route::middleware('cerberus.permission:/attendance')->group(function () {
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])
                ->name('index');
            Route::get('/triage', [AttendanceController::class, 'triage'])
                ->name('triage');
            Route::post('/triage', [AttendanceController::class, 'storeTriage'])
                ->name('triage.store');
            Route::patch('/status/{id}', [AttendanceController::class, 'updateStatus'])
                ->name('updateStatus');
            Route::get('/api/patients/search', [AttendanceController::class, 'searchPatient'])
                ->name('patients.search');
        });
    });

    // ========================================
    // PROFISSIONAL - Painel do Profissional (Consultas)
    // ========================================
    Route::middleware('cerberus.permission:/professional')->group(function () {
        Route::prefix('professional')->name('professional.')->group(function () {
            Route::get('/', [ProfessionalController::class, 'index'])
                ->name('index');
            Route::get('/search-patient', [ProfessionalController::class, 'searchPatient'])
                ->name('searchPatient');
            Route::get('/consultation/{id}', [ProfessionalController::class, 'startConsultation'])
                ->name('consultation');
            Route::post('/generate-prescription/{id}', [ProfessionalController::class, 'generatePrescription'])
                ->name('generatePrescription');
            Route::post('/send-whatsapp', [ProfessionalController::class, 'sendWhatsApp'])
                ->name('sendWhatsApp');
            Route::post('/send-exam-whatsapp', [ProfessionalController::class, 'sendExamWhatsApp'])
                ->name('sendExamWhatsApp');
            Route::post('/send-referral-whatsapp', [ProfessionalController::class, 'sendReferralWhatsApp'])
                ->name('sendReferralWhatsApp');
            Route::post('/refer-patient/{id}', [ProfessionalController::class, 'referPatient'])
                ->name('referPatient');
            Route::post('/finish-consultation/{id}', [ProfessionalController::class, 'finishConsultation'])
                ->name('finishConsultation');
            Route::get('/patient-history/{id}', [ProfessionalController::class, 'patientHistory'])
                ->name('patientHistory');
            Route::get('/patient-history-full/{id}', [ProfessionalController::class, 'patientHistoryFull'])
                ->name('patientHistoryFull');
            Route::get('/print-prescription/{id}', [ProfessionalController::class, 'printPrescription'])
                ->name('printPrescription');
            Route::get('/print-exame/{id}', [ProfessionalController::class, 'printExamDoc'])
                ->name('print-exame');
            Route::get('/print-referral/{id}', [ProfessionalController::class, 'printReferralDoc'])
                ->name('print-referral');
            Route::get('/new-prescription', [ProfessionalController::class, 'newPrescription'])
                ->name('newPrescription');
            Route::post('/new-prescription/store', [ProfessionalController::class, 'storeNewPrescription'])
                ->name('storeNewPrescription');
            Route::post('/update-status/{id}', [ProfessionalController::class, 'updateStatus'])
                ->name('update-status');
            Route::post('/save-prescription-draft/{id}', [ProfessionalController::class, 'savePrescriptionDraft'])
                ->name('savePrescriptionDraft');
            Route::post('/save-exame/{id}', [ProfessionalController::class, 'saveExame'])
                ->name('saveExame');
        });
    });
});
```

## Configuração no Cerberus

Para que as permissões funcionem, você precisa cadastrar os items no Cerberus:

### Items Principais (com submenus)

```
1. Dashboard
   - URL: /dashboard
   - Icon: mdi mdi-view-dashboard
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 1

2. Pacientes
   - URL: /pessoas
   - Icon: mdi mdi-account-multiple
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 2
   - Filhos:
     ├── Listar Pacientes (/pessoas)
     └── Novo Paciente (/pessoas/create)

3. Recepção
   - URL: /recepcao
   - Icon: mdi mdi-desk
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 3
   - Filhos:
     ├── Fila de Atendimento (/recepcao)
     └── Triagem (/recepcao/triage)

4. Atendimento
   - URL: /attendance
   - Icon: mdi mdi-stethoscope
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 4
   - Filhos:
     └── Fila de Atendimento (/attendance)

5. Profissional
   - URL: /professional
   - Icon: mdi mdi-doctor
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 5
   - Filhos:
     ├── Consultas (/professional)
     └── Nova Prescrição (/professional/new-prescription)

6. Produtos
   - URL: /produtos
   - Icon: mdi mdi-package-variant
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 6
   - Filhos:
     ├── Listar Produtos (/produtos)
     ├── Novo Produto (/produtos/create)
     ├── Categorias (/categorias)
     └── Laboratórios (/laboratorios)

7. Profissionais (Gestão)
   - URL: /profissionais
   - Icon: mdi mdi-account-badge
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 7
   - Filhos:
     ├── Listar Profissionais (/profissionais)
     └── Novo Profissional (/profissionais/create)

8. Administração
   - URL: /admin/users
   - Icon: mdi mdi-cog
   - Type Menu: left_sidebar
   - Show Menu: true
   - Ordering: 8
   - Filhos:
     ├── Usuários (/admin/users)
     └── Configurações (/admin/config)
```

## Perfis Sugeridos

### 1. Administrador
- Tem acesso a TODOS os items

### 2. Médico
- Dashboard
- Profissional (consultas)
- Pacientes (visualizar)
- Atendimento (visualizar)

### 3. Recepcionista
- Dashboard
- Recepção
- Pacientes
- Atendimento (visualizar)

### 4. Enfermeiro
- Dashboard
- Atendimento
- Pacientes (visualizar)
- Recepção (visualizar)

### 5. Farmacêutico
- Dashboard
- Produtos
- Pacientes (visualizar)

## Permissões Granulares (Opcional)

Você pode ter permissões mais específicas, por exemplo:

```php
// Permitir visualizar mas não editar
Route::get('/pessoas', [PessoaController::class, 'index'])
    ->middleware('cerberus.permission:/pessoas');

Route::get('/pessoas/create', [PessoaController::class, 'create'])
    ->middleware('cerberus.permission:/pessoas/create');

Route::post('/pessoas', [PessoaController::class, 'store'])
    ->middleware('cerberus.permission:/pessoas/create');

Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])
    ->middleware('cerberus.permission:/pessoas/edit');

Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])
    ->middleware('cerberus.permission:/pessoas/edit');

Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])
    ->middleware('cerberus.permission:/pessoas/delete');
```

Neste caso, no Cerberus você criaria:
- `/pessoas` - Visualizar pacientes
- `/pessoas/create` - Criar pacientes
- `/pessoas/edit` - Editar pacientes
- `/pessoas/delete` - Excluir pacientes

## Múltiplas Permissões (OR)

Se quiser que o usuário precise de QUALQUER UMA das permissões:

```php
// Usuário precisa ter /dashboard OU /admin para acessar
Route::get('/statistics', [StatsController::class, 'index'])
    ->middleware('cerberus.permission:/dashboard,/admin');
```

## Verificações Adicionais no Controller

Você também pode fazer verificações extras nos controllers:

```php
namespace App\Http\Controllers;

use App\Helpers\AuthHelper;

class PessoaController extends Controller
{
    public function destroy($id)
    {
        // Verificação adicional além do middleware
        if (!AuthHelper::hasPermission('/pessoas/delete')) {
            abort(403, 'Você não tem permissão para excluir pacientes');
        }
        
        // Lógica de exclusão
    }
}
```

## Testando

1. Crie os items no Cerberus
2. Atribua aos perfis
3. Vincule usuário ao perfil
4. Faça login no VisaoSis
5. Verifique se o menu aparece corretamente
6. Tente acessar URLs diretamente (deve bloquear se não tiver permissão)

## Resumo

✅ Todas as rotas estão protegidas por autenticação (`auth`)
✅ Rotas sensíveis têm permissão específica do Cerberus
✅ Menu é montado automaticamente baseado nas permissões
✅ Acesso direto a URLs é bloqueado se não tiver permissão
✅ Sistema suporta múltiplos perfis e permissões granulares

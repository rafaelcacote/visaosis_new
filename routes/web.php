<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\ProfissionalWorkflowController;
use App\Http\Controllers\RecepcaoController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API para informações do usuário (usada durante o login)
Route::post('/api/user/info', [AuthController::class, 'getUserInfo']);

Route::prefix('public')->name('public.')->group(function () {
    Route::get('/prescription/{token}', [ProfissionalWorkflowController::class, 'viewPrescriptionByToken'])->name('prescription.view');
    Route::get('/exam/{token}', [ProfissionalWorkflowController::class, 'viewExamByToken'])->name('exam.view');
    Route::get('/referral/{token}', [ProfissionalWorkflowController::class, 'viewReferralByToken'])->name('referral.view');
});

// ============================================
// ROTAS DE DEBUG - Menu e Sessão
// ============================================
// View visual de debug
Route::get('/debug/menu', function () {
    return view('debug.menu');
})->middleware('auth')->name('debug.menu');

// API JSON de debug
Route::get('/debug/menu/json', function () {
    if (! auth()->check()) {
        return response()->json([
            'error' => 'Usuário não autenticado',
            'message' => 'Faça login primeiro',
        ]);
    }

    return response()->json([
        'auth_check' => auth()->check(),
        'user_id' => auth()->id(),
        'user_name' => auth()->user()->name ?? null,
        'session_data' => [
            'has_cerberus_token' => session()->has('cerberusToken'),
            'cerberus_token' => session('cerberusToken') ? substr(session('cerberusToken'), 0, 20) . '...' : null,
            'has_items' => session()->has('items'),
            'items_count' => count(session('items', [])),
            'items_raw' => session('items', []),
            'perfis' => session('perfis', []),
            'tenant_id' => session('tenant_id'),
            'tenant_name' => session('tenant') ? (session('tenant')->name ?? null) : null,
            'location_id' => session('location_id'),
        ],
        'menu_helper' => [
            'left_sidebar_items' => \App\Helpers\MenuHelper::getMenuItems('left_sidebar'),
            'topnav_items' => \App\Helpers\MenuHelper::getMenuItems('topnav'),
        ],
        'auth_helper' => [
            'check' => \App\Helpers\AuthHelper::check(),
            'name' => \App\Helpers\AuthHelper::name(),
            'email' => \App\Helpers\AuthHelper::email(),
            'permissions_count' => count(\App\Helpers\AuthHelper::permissions()),
            'has_dashboard_permission' => \App\Helpers\AuthHelper::hasPermission('/dashboard'),
        ],
    ]);
})->middleware('auth')->name('debug.menu.json');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rota para seleção de location
    Route::post('/location/select', [AuthController::class, 'selectLocation'])->name('location.select');

    // Rotas de usuários
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');

    // Rotas de profissionais
    Route::resource('profissionais', ProfissionalController::class)->parameters([
        'profissionais' => 'profissional',
    ]);
    Route::get('profissionais/search', [ProfissionalController::class, 'search'])->name('profissionais.search');
    Route::post('profissionais/{profissional}/toggle-status', [ProfissionalController::class, 'toggleStatus'])->name('profissionais.toggle-status');

    // Rotas de categorias
    Route::resource('categorias', CategoriaController::class)->parameters([
        'categorias' => 'categoria',
    ]);
    Route::post('categorias/{categoria}/toggle-status', [CategoriaController::class, 'toggleStatus'])->name('categorias.toggle-status');

    // Rotas de laboratórios
    Route::resource('laboratorios', LaboratorioController::class)->parameters([
        'laboratorios' => 'laboratorio',
    ]);
    Route::post('laboratorios/{laboratorio}/toggle-status', [LaboratorioController::class, 'toggleStatus'])->name('laboratorios.toggle-status');

    // Rotas de produtos
    Route::resource('produtos', ProdutoController::class)->parameters([
        'produtos' => 'produto',
    ]);
    Route::post('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])->name('produtos.toggle-status');

    // Rotas de pacientes (pessoas)
    Route::get('pessoas/search', [PessoaController::class, 'search'])->name('pessoas.search');
    Route::get('pessoas/{pessoa}/receitas', [PessoaController::class, 'receitas'])->name('pessoas.receitas');
    Route::post('pessoas/{pessoa}/receitas', [PessoaController::class, 'storePrescription'])->name('pessoas.receitas.store');
    Route::resource('pessoas', PessoaController::class)->parameters([
        'pessoas' => 'pessoa',
    ]);
    Route::post('pessoas/{pessoa}/toggle-status', [PessoaController::class, 'toggleStatus'])->name('pessoas.toggle-status');

    // Módulo de Recepção
    Route::get('/recepcao', [RecepcaoController::class, 'index'])->name('recepcao.index');
    Route::prefix('recepcao')->name('recepcao.')->group(function () {
        Route::get('/dashboard', [RecepcaoController::class, 'dashboard'])->name('dashboard');
        Route::get('/triage', [RecepcaoController::class, 'triage'])->name('triage');
        Route::post('/triage', [RecepcaoController::class, 'storeTriage'])->name('triage.store');
        Route::get('/consulta/{consulta}', [RecepcaoController::class, 'show'])->name('consulta.show');
        Route::post('/checkin', [RecepcaoController::class, 'checkin'])->name('checkin');
        Route::patch('/status/{consulta}', [RecepcaoController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/api/patients/search', [RecepcaoController::class, 'searchPatient'])->name('patients.search');
    });

    // Módulo de Atendimento (Attendance)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/triage', [AttendanceController::class, 'triage'])->name('triage');
        Route::post('/triage', [AttendanceController::class, 'storeTriage'])->name('triage.store');
        Route::patch('/status/{id}', [AttendanceController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/api/patients/search', [AttendanceController::class, 'searchPatient'])->name('patients.search');
    });

    // Módulo Profissional (Workflow de Atendimento)
    Route::prefix('professional')->name('professional.')->group(function () {
        Route::get('/', [ProfissionalWorkflowController::class, 'index'])->name('index');
        Route::get('/search-patient', [ProfissionalWorkflowController::class, 'searchPatient'])->name('searchPatient');
        Route::get('/consultation/{id}', [ProfissionalWorkflowController::class, 'startConsultation'])->name('consultation');
        Route::post('/generate-prescription/{id}', [ProfissionalWorkflowController::class, 'generatePrescription'])->name('generatePrescription');
        Route::post('/send-whatsapp', [ProfissionalWorkflowController::class, 'sendWhatsApp'])->name('sendWhatsApp');
        Route::post('/send-exam-whatsapp', [ProfissionalWorkflowController::class, 'sendExamWhatsApp'])->name('sendExamWhatsApp');
        Route::post('/send-referral-whatsapp', [ProfissionalWorkflowController::class, 'sendReferralWhatsApp'])->name('sendReferralWhatsApp');
        Route::post('/refer-patient/{id}', [ProfissionalWorkflowController::class, 'referPatient'])->name('referPatient');
        Route::post('/finish-consultation/{id}', [ProfissionalWorkflowController::class, 'finishConsultation'])->name('finishConsultation');
        Route::get('/patient-history/{id}', [ProfissionalWorkflowController::class, 'patientHistory'])->name('patientHistory');
        Route::get('/patient-history-full/{id}', [ProfissionalWorkflowController::class, 'patientHistoryFull'])->name('patientHistoryFull');
        Route::get('/print-prescription/{id}', [ProfissionalWorkflowController::class, 'printPrescription'])->name('printPrescription');
        Route::get('/print-exame/{id}', [ProfissionalWorkflowController::class, 'printExamDoc'])->name('print-exame');
        Route::get('/print-referral/{id}', [ProfissionalWorkflowController::class, 'printReferralDoc'])->name('print-referral');
        Route::get('/new-prescription', [ProfissionalWorkflowController::class, 'newPrescription'])->name('newPrescription');
        Route::post('/new-prescription/store', [ProfissionalWorkflowController::class, 'storeNewPrescription'])->name('storeNewPrescription');
        Route::post('/update-status/{id}', [ProfissionalWorkflowController::class, 'updateStatus'])->name('update-status');
        Route::post('/save-prescription-draft/{id}', [ProfissionalWorkflowController::class, 'savePrescriptionDraft'])->name('savePrescriptionDraft');
        Route::post('/save-exame/{id}', [ProfissionalWorkflowController::class, 'saveExame'])->name('saveExame');
    });

    // Módulo de Vendas
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{id}/print', [SaleController::class, 'print'])->name('sales.print');

    // Módulo de Ordens de Serviço
    Route::resource('ordens-servico', OrdemServicoController::class)->parameters([
        'ordens-servico' => 'ordemServico',
    ]);
    Route::get('/ordens-servico/{ordemServico}/pdf', [OrdemServicoController::class, 'pdf'])->name('ordens-servico.pdf');
    Route::get('/ordens-servico/api/buscar-clientes', [OrdemServicoController::class, 'buscarClientes'])->name('ordens-servico.buscar-clientes');
    Route::get('/ordens-servico/api/buscar-vendas-cliente', [OrdemServicoController::class, 'buscarVendasCliente'])->name('ordens-servico.buscar-vendas-cliente');
    Route::get('/ordens-servico/api/buscar-prescricoes', [OrdemServicoController::class, 'buscarPrescricoes'])->name('ordens-servico.buscar-prescricoes');

    // Módulo Financeiro
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::get('/receivables', [FinancialController::class, 'receivables'])->name('receivables');
        Route::get('/boletos', [FinancialController::class, 'boletos'])->name('boletos');
        Route::get('/notifications', [FinancialController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/templates', [FinancialController::class, 'templates'])->name('notifications.templates');
        Route::post('/notifications/templates', [FinancialController::class, 'saveTemplates'])->name('notifications.templates.save');
        Route::post('/notifications/send', [FinancialController::class, 'sendNotification'])->name('notifications.send');
        Route::post('/notifications/schedule-batch', [FinancialController::class, 'scheduleBatch'])->name('notifications.schedule-batch');
        Route::post('/notifications/{id}/resend', [FinancialController::class, 'resendNotification'])->name('notifications.resend');
        Route::post('/notifications/clear-history', [FinancialController::class, 'clearNotificationHistory'])->name('notifications.clear-history');
        Route::post('/notifications/{id}/update', [FinancialController::class, 'updateNotification'])->name('notifications.update');
        Route::post('/notifications/{id}/cancel', [FinancialController::class, 'cancelNotification'])->name('notifications.cancel');

        // Ações AJAX
        Route::post('/generate-boleto/{id}', [FinancialController::class, 'generateBoleto'])->name('generate-boleto');
        Route::get('/boleto-pdf/{id}', [FinancialController::class, 'boletoPdf'])->name('boleto-pdf');
        Route::post('/send-whatsapp/{id}', [FinancialController::class, 'sendWhatsApp'])->name('send-whatsapp');
        Route::post('/receive-payment', [FinancialController::class, 'receivePayment'])->name('receive-payment');
    });
    Route::post('/professional/toggle-pause/{id}', [ProfissionalWorkflowController::class, 'togglePause']);

    // Relatórios
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('index');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');
    });
});

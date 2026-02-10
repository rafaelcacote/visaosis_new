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

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API para informações do usuário (usada durante o login)
Route::post('/api/user/info', [AuthController::class, 'getUserInfo']);

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
        'profissionais' => 'profissional'
    ]);
    Route::get('profissionais/search', [ProfissionalController::class, 'search'])->name('profissionais.search');
    Route::post('profissionais/{profissional}/toggle-status', [ProfissionalController::class, 'toggleStatus'])->name('profissionais.toggle-status');
    
    // Rotas de categorias
    Route::resource('categorias', CategoriaController::class)->parameters([
        'categorias' => 'categoria'
    ]);
    Route::post('categorias/{categoria}/toggle-status', [CategoriaController::class, 'toggleStatus'])->name('categorias.toggle-status');

    // Rotas de laboratórios
    Route::resource('laboratorios', LaboratorioController::class)->parameters([
        'laboratorios' => 'laboratorio'
    ]);
    Route::post('laboratorios/{laboratorio}/toggle-status', [LaboratorioController::class, 'toggleStatus'])->name('laboratorios.toggle-status');

    // Rotas de produtos
    Route::resource('produtos', ProdutoController::class)->parameters([
        'produtos' => 'produto'
    ]);
    Route::post('produtos/{produto}/toggle-status', [ProdutoController::class, 'toggleStatus'])->name('produtos.toggle-status');

    // Rotas de pacientes (pessoas)
    Route::get('pessoas/search', [PessoaController::class, 'search'])->name('pessoas.search');
    Route::resource('pessoas', PessoaController::class)->parameters([
        'pessoas' => 'pessoa',
    ]);
    Route::post('pessoas/{pessoa}/toggle-status', [PessoaController::class, 'toggleStatus'])->name('pessoas.toggle-status');
});

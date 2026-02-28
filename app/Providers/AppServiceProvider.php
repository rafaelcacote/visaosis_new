<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usar Bootstrap 5 como tema padrão para paginação
        Paginator::defaultView('pagination::bootstrap-5');
        
        // Carregar helpers
        if (file_exists(app_path('Helpers/MenuHelper.php'))) {
            require_once app_path('Helpers/MenuHelper.php');
        }
    }
}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connect Plus')</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    @stack('plugin-css')
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-fix.css') }}">
    <!-- End layout styles -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <!-- Vite HMR for auto-reload -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
  </head>
  <body>
    <div class="container-scroller">
      
      @include('components.navbar')
      <div class="container-fluid page-body-wrapper">
        @include('components.sidebar')
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
          </div>
          @include('components.footer')
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>

    <!-- Toasts (Connect Plus / Bootstrap) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
      @if (session('success'))
        <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
          <div class="d-flex">
            <div class="toast-body">
              <i class="mdi mdi-check-circle me-2"></i>
              {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      @endif

      @if (session('warning'))
        <div class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6500">
          <div class="d-flex">
            <div class="toast-body">
              <i class="mdi mdi-alert-circle me-2"></i>
              {{ session('warning') }}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      @endif

      @if (session('info'))
        <div class="toast align-items-center text-bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5500">
          <div class="d-flex">
            <div class="toast-body">
              <i class="mdi mdi-information-outline me-2"></i>
              {{ session('info') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      @endif

      @if (session('error'))
        <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="8000">
          <div class="d-flex">
            <div class="toast-body">
              <i class="mdi mdi-alert-outline me-2"></i>
              {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      @endif

      {{-- Erros do validator / withErrors(['error' => ...]) --}}
      @if (!session('error') && $errors->any())
        <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="9000">
          <div class="d-flex">
            <div class="toast-body">
              <i class="mdi mdi-alert-outline me-2"></i>
              {{ $errors->first() }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      @endif
    </div>

    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    @stack('plugin-js')
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    @stack('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // Initialize toasts
        if (window.bootstrap && window.bootstrap.Toast) {
          document.querySelectorAll('.toast-container .toast').forEach(function (el) {
            try {
              const t = window.bootstrap.Toast.getOrCreateInstance(el, {
                autohide: true
              });
              t.show();
            } catch (e) {
              // no-op
            }
          });
        }

        // Initialize sidebar collapses
        if (window.bootstrap && window.bootstrap.Collapse) {
          // Pré-inicializa todos os elementos collapse da sidebar
          document.querySelectorAll('.sidebar .collapse').forEach(function (collapseEl) {
            // Cria instância do collapse com configurações adequadas
            window.bootstrap.Collapse.getOrCreateInstance(collapseEl, {
              toggle: false, // Não alterna automaticamente na inicialização
              parent: false  // Permite múltiplos dropdowns abertos
            });
          });

          // Adiciona event listeners para os botões de toggle
          document.querySelectorAll('.sidebar [data-bs-toggle="collapse"]').forEach(function (toggleEl) {
            toggleEl.addEventListener('click', function(e) {
              e.preventDefault();
              e.stopPropagation();
              
              const targetId = this.getAttribute('href') || this.getAttribute('data-bs-target');
              const targetEl = document.querySelector(targetId);
              
              if (targetEl) {
                const bsCollapse = window.bootstrap.Collapse.getInstance(targetEl);
                if (bsCollapse) {
                  bsCollapse.toggle();
                }
              }
            });
          });

          // Event listeners para manter o estado visual correto
          document.querySelectorAll('.sidebar .collapse').forEach(function (collapseEl) {
            collapseEl.addEventListener('show.bs.collapse', function() {
              // Adiciona classe show para garantir visibilidade
              this.classList.add('show');
            });
            
            collapseEl.addEventListener('shown.bs.collapse', function() {
              // Garante que o elemento permanece visível após animação
              this.style.display = 'block';
            });
          });
        }
      });
    </script>
    <!-- End custom js for this page -->
  </body>
</html>
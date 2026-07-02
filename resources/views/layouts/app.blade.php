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
    <!-- End layout styles -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <!-- Vite HMR for auto-reload -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- sidebar-fix DEVE vir após style e vite para garantir submenus visíveis em todos os ambientes -->
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/show-pages.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/content-readability.css') }}">
    <style>
        .tag {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1;
            border-radius: 0.375rem;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .tag i {
            margin-right: 0.25rem;
        }

        /* Padronização de fonte seguindo padrão da tabela Fila de Atendimento de recepção */
        .card-body h6 {
            font-size: 1rem !important;
            font-weight: 500 !important;
            margin-bottom: 0.25rem !important;
        }

        .card-body small.text-muted {
            font-size: 0.875rem !important;
            font-weight: 400 !important;
        }

        .card-body label.small,
        .card-body .text-muted.small {
            font-size: 0.875rem !important;
            font-weight: 400 !important;
        }

        .card-body p {
            font-size: 1rem !important;
            font-weight: 500 !important;
        }

        .card-body .fw-medium,
        .card-body span.fw-medium {
            font-weight: 500 !important;
        }

        .card-statistics .card-body {
            padding: 1rem !important;
        }

        .card-statistics .icon-lg {
            font-size: 1.9rem;
            line-height: 1;
        }

        .card-statistics h3 {
            font-size: 1.25rem;
            line-height: 1.1;
        }

        .card-statistics p {
            font-size: 0.8rem !important;
            line-height: 1.2;
            font-weight: 400 !important;
        }

        .card-statistics .text-muted.mt-3 {
            margin-top: 0.5rem !important;
        }
    </style>

    {{-- Estilos do menu de perfil (aqui pois o navbar é @include no body, após o @stack) --}}
    <style>
        /* ── Posicionamento base ─────────────────────────────────── */
        .navbar-menu-wrapper { overflow: visible !important; }
        .navbar-nav { overflow: visible !important; }
        li.nav-item.nav-profile { position: relative !important; }

        /* ── Trigger: botão do menu de usuário ──────────────────── */
        li.nav-item.nav-profile a.nav-profile-trigger {
            display: flex !important;
            align-items: center !important;
            gap: 0.45rem !important;
            padding: 0.3rem 0.75rem 0.3rem 0.6rem !important;
            border-radius: 8px !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            text-decoration: none !important;
            color: #343a40 !important;
            cursor: pointer;
            transition: background 0.18s ease, border-color 0.18s ease;
            line-height: 1 !important;
        }

        li.nav-item.nav-profile a.nav-profile-trigger:hover,
        li.nav-item.nav-profile a.nav-profile-trigger.open {
            background: #f4f5f8 !important;
            border-color: #e2e5ef !important;
        }

        /* Remove o caret padrão do Bootstrap */
        li.nav-item.nav-profile a.nav-profile-trigger::after {
            display: none !important;
        }

        li.nav-item.nav-profile a.nav-profile-trigger .trigger-icon {
            font-size: 1.4rem;
            color: #6c757d;
            flex-shrink: 0;
            line-height: 1;
        }

        li.nav-item.nav-profile a.nav-profile-trigger .trigger-name {
            font-size: 0.84rem;
            font-weight: 600;
            color: #343a40;
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1;
        }

        li.nav-item.nav-profile a.nav-profile-trigger .trigger-chevron {
            font-size: 1rem;
            color: #adb5bd;
            line-height: 1;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        li.nav-item.nav-profile a.nav-profile-trigger.open .trigger-chevron {
            transform: rotate(180deg);
        }

        /* ── Dropdown ────────────────────────────────────────────── */
        li.nav-item.nav-profile .profile-dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            min-width: 240px !important;
            margin-top: 10px !important;
            padding: 0 !important;
            border: 1px solid #e4e6ef !important;
            border-radius: 12px !important;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.10) !important;
            overflow: hidden !important;
        }

        /* Cabeçalho */
        .pd-header {
            padding: 1rem 1.1rem 0.9rem;
            background: #f8f9fb;
            border-bottom: 1px solid #eef0f5;
        }

        .pd-header-name {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1a1d23;
            margin: 0 0 0.15rem;
            line-height: 1.35;
            word-break: break-word;
        }

        .pd-header-email {
            font-size: 0.74rem;
            color: #8b93a5;
            margin: 0;
            line-height: 1.3;
            word-break: break-all;
        }

        /* Itens */
        .pd-items {
            padding: 0.4rem 0.45rem;
        }

        .pd-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.6rem !important;
            padding: 0.6rem 0.75rem !important;
            border-radius: 8px !important;
            font-size: 0.84rem !important;
            font-weight: 500 !important;
            color: #343a40 !important;
            text-decoration: none !important;
            background: transparent !important;
            border: none !important;
            width: 100% !important;
            text-align: left !important;
            cursor: pointer;
            transition: background 0.14s ease, color 0.14s ease;
            line-height: 1.2 !important;
        }

        .pd-item:hover {
            background: #f1f3f9 !important;
            color: #1a1d23 !important;
        }

        .pd-item i {
            font-size: 1.05rem;
            color: #8b93a5;
            flex-shrink: 0;
            width: 18px;
            text-align: center;
            transition: color 0.14s ease;
            line-height: 1;
        }

        .pd-item:hover i {
            color: #495057;
        }

        .pd-divider {
            height: 1px;
            background: #eef0f5;
            margin: 0.35rem 0.45rem;
        }

        .pd-item--danger:hover {
            background: #fff5f5 !important;
            color: #dc3545 !important;
        }

        .pd-item--danger:hover i {
            color: #dc3545 !important;
        }

        /* Form dentro do pd-items não quebra layout */
        .pd-items form {
            display: block;
        }

        @media (max-width: 991px) {
            li.nav-item.nav-profile .profile-dropdown-menu {
                right: 4px !important;
            }
            li.nav-item.nav-profile a.nav-profile-trigger .trigger-name {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="container-scroller">

        @include('components.navbar')
        <div class="container-fluid page-body-wrapper">
            @include('components.sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @if (View::hasSection('page-title') || View::hasSection('page-actions'))
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        @hasSection('page-title')
                                            <h2 class="text-dark font-weight-bold mb-1">
                                                @yield('page-title')
                                            </h2>
                                        @endif
                                    </div>
                                    <div>
                                        @yield('page-actions')
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @yield('content')
                </div>
                @include('components.footer')
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    @auth
        @include('components.profile-change-password-modal')
    @endauth

    <!-- Toasts (Connect Plus / Bootstrap) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4500">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="mdi mdi-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="6500">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="toast align-items-center text-bg-info border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="5500">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="mdi mdi-information-outline me-2"></i>
                        {{ session('info') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="8000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        {{-- Erros do validator / withErrors(['error' => ...]) --}}
        @if (!session('error') && $errors->any())
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="9000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        {{ $errors->first() }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="appMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="appMessageModalHeader">
                    <h5 class="modal-title" id="appMessageModalTitle">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="appMessageModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
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
        window.addEventListener('load', function() {
            if (window.bootstrap && window.bootstrap.Toast) {
                document.querySelectorAll('.toast-container .toast').forEach(function(el) {
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
        });
    </script>
    <script>
        (function() {
            function getVariantClass(variant) {
                const v = (variant || 'primary').toString();
                if (v === 'success') return 'bg-success text-white';
                if (v === 'danger') return 'bg-danger text-white';
                if (v === 'warning') return 'bg-warning';
                if (v === 'info') return 'bg-info';
                return 'bg-primary text-white';
            }

            window.showAppModalMessage = function(message, title, variant) {
                const modalEl = document.getElementById('appMessageModal');
                if (!modalEl || !window.bootstrap || !window.bootstrap.Modal) {
                    return;
                }

                const header = document.getElementById('appMessageModalHeader');
                const titleEl = document.getElementById('appMessageModalTitle');
                const bodyEl = document.getElementById('appMessageModalBody');

                if (titleEl) titleEl.textContent = title || 'Mensagem';
                if (bodyEl) bodyEl.textContent = message || '';

                if (header) {
                    header.className = 'modal-header ' + getVariantClass(variant);
                }

                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            };
        })();
    </script>
    <!-- End custom js for this page -->
</body>

</html>

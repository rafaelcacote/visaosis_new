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

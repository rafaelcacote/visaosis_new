{{-- Removido use DB - não é mais necessário, dados vêm da sessão --}}

@push('styles')
<style>
  /* Garantir que o navbar apareça */
  .default-layout-navbar {
    display: flex !important;
    visibility: visible !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1000 !important;
    background-color: #fff !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
  }

  /* Hover effect para o botão de logout no dropdown */
  .dropdown-menu.navbar-dropdown form button.dropdown-item:hover,
  .dropdown-menu.navbar-dropdown form button.dropdown-item:focus {
    background-color: #f8f9fa !important;
    color: #16181b !important;
    text-decoration: none;
  }
  
  /* Garantir que o botão se comporte como os outros dropdown-items */
  .dropdown-menu.navbar-dropdown form {
    margin: 0;
    padding: 0;
  }
  
  .dropdown-menu.navbar-dropdown form button.dropdown-item {
    width: 100%;
    text-align: left;
  }

  /* Estilo para location ativa */
  .dropdown-menu.navbar-dropdown form button.dropdown-item.active {
    background-color: rgba(102, 126, 234, 0.1);
  }

  /* Nome do tenant no navbar */
  .navbar-brand-wrapper .tenant-name-text-only {
    color: #ffffff !important;
    font-size: 0.95rem;
    font-weight: 600;
    letter-spacing: 0.2px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    max-width: 200px;
    display: inline-block;
    padding: 0.15rem 0.6rem;
    border-radius: 4px;
    background: #181824;
  }

  /* Garantir que o link do navbar-brand não sobrescreva a cor */
  .navbar-brand-wrapper .navbar-brand,
  .navbar-brand-wrapper .navbar-brand:hover,
  .navbar-brand-wrapper .navbar-brand:focus,
  .navbar-brand-wrapper .navbar-brand:active {
    color: inherit !important;
  }

  .navbar-brand-wrapper .navbar-brand .tenant-name-text-only {
    color: #ffffff !important;
  }

  /* Logo principal no navbar brand */
  .navbar-logo-main {
    max-height: 60px !important;
    max-width: 200px !important;
    height: 60px !important;
    width: auto !important;
    object-fit: contain !important;
    object-position: center !important;
    display: block !important;
    margin: 0 auto !important;
  }

  /* Garantir que imagens dentro do navbar-brand-wrapper respeitem o tamanho */
  .navbar-brand-wrapper img,
  .navbar-brand-wrapper .navbar-logo-main,
  .navbar-brand-wrapper .brand-logo img {
    max-height: 60px !important;
    max-width: 200px !important;
    height: 60px !important;
    width: auto !important;
  }

  /* Centralizar o navbar-brand */
  .navbar-brand-wrapper .navbar-brand.brand-logo {
    justify-content: center !important;
    align-items: center !important;
    width: 100%;
  }

  /* Estilos para logo e nome do tenant no lugar do search */
  .tenant-brand-display {
    padding: 0.5rem 1rem;
    gap: 0.75rem;
  }

  .tenant-logo-container {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .tenant-logo-navbar {
    max-height: 24px !important;
    max-width: 80px !important;
    height: 24px !important;
    width: auto !important;
    object-fit: contain !important;
    object-position: center !important;
    display: block !important;
  }

  /* Garantir que imagens dentro do search-field respeitem o tamanho */
  .search-field img,
  .search-field .tenant-logo-navbar {
    max-height: 24px !important;
    max-width: 80px !important;
    height: auto !important;
    width: auto !important;
  }

  .tenant-name-navbar {
    color: #212529;
    font-size: 1rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
  }


</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chevron animado no trigger do perfil
    const profileTrigger = document.getElementById('profileDropdown');
    if (profileTrigger) {
        profileTrigger.addEventListener('show.bs.dropdown', function() {
            this.classList.add('open');
        });
        profileTrigger.addEventListener('hide.bs.dropdown', function() {
            this.classList.remove('open');
        });
    }

    // Adicionar loading ao trocar location
    const locationForms = document.querySelectorAll('.location-switch-form');
    
    locationForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            const originalContent = button.innerHTML;
            
            // Desabilitar botão e mostrar loading
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Alterando...';
            
            // Se não recarregar em 5 segundos, restaurar botão
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalContent;
            }, 5000);
        });
    });
});
</script>
@endpush
<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    @php
      // Buscar tenant da sessão (já cacheado no login/selectLocation)
      $tenant = session('tenant');

      // Se não encontrou tenant, usar nome padrão do sistema
      if (!$tenant) {
        $tenantName = 'VisaoSis';
        $tenantLogo = asset('assets/images/logo.svg');
      } else {
        // Usa dados do tenant da sessão (sem queries ao banco)
        $tenantName = (property_exists($tenant, 'name') && $tenant->name) 
          ? $tenant->name 
          : ((property_exists($tenant, 'trade_name') && $tenant->trade_name) 
            ? $tenant->trade_name 
            : 'VisaoSis');
        
        // Constrói a URL da logo principal
        $logoPath = property_exists($tenant, 'logo_path') ? $tenant->logo_path : null;
        if (!empty($logoPath)) {
          // Se já é uma URL completa, retorna como está
          if (filter_var($logoPath, FILTER_VALIDATE_URL)) {
            $tenantLogo = $logoPath;
          } else {
            // Se é um caminho relativo, constrói a URL completa do Cerberus
            $cerberusUrl = env('CERBERUS_URL', 'http://localhost:8000');
            $tenantLogo = rtrim($cerberusUrl, '/') . '/storage/' . ltrim($logoPath, '/');
          }
        } else {
          $tenantLogo = asset('assets/images/logo.svg');
        }
      }
    @endphp
    <a class="navbar-brand brand-logo d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
      <img src="{{ asset('assets/images/visaosis_logo.svg') }}" alt="VisaoSis" class="navbar-logo-main" style="max-height: 60px; max-width: 200px; height: 60px; width: auto; object-fit: contain;" />
    </a>
  
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-stretch">
    <ul class="navbar-nav navbar-nav-right">
      @auth
      @if(session('user_locations') && count(session('user_locations')) > 1)
      <li class="nav-item dropdown d-none d-md-block">
        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center" id="locationDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="mdi mdi-store me-2"></i>
          @if(session('location'))
            <span class="me-2">{{ session('location')->name ?? 'Localidade' }}</span>
            <span class="count-symbol bg-primary"></span>
          @else
            <span class="me-2">Selecionar Localidade</span>
          @endif
        </a>
        <div class="dropdown-menu dropdown-menu-end navbar-dropdown preview-list" aria-labelledby="locationDropdown" data-bs-popper="static">
          <h6 class="p-3 mb-0 bg-primary text-white py-4">Trocar Localidade</h6>
          <div class="dropdown-divider"></div>
          @foreach(session('user_locations', []) as $userLocation)
            @php
              $isActive = session('location_id') == $userLocation['location_id'];
              $locationName = $userLocation['location']['name'] ?? 'Location';
              $locationShort = $userLocation['location']['short_name'] ?? '';
              $previewColors = ['bg-success', 'bg-warning', 'bg-info', 'bg-primary'];
              $colorIndex = $loop->index % count($previewColors);
              $previewIconClass = $isActive ? 'bg-primary' : $previewColors[$colorIndex];
            @endphp
            <form method="POST" action="{{ route('location.select') }}" class="location-switch-form d-inline w-100">
              @csrf
              <input type="hidden" name="location_id" value="{{ $userLocation['location_id'] }}">
              <button type="submit" class="dropdown-item preview-item w-100 border-0 bg-transparent text-start {{ $isActive ? 'active' : '' }}" style="cursor: pointer;">
                <div class="preview-thumbnail">
                  <div class="preview-icon {{ $previewIconClass }}">
                    <i class="mdi mdi-store"></i>
                  </div>
                </div>
                <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                  <h6 class="preview-subject font-weight-normal mb-1">{{ $locationName }}{{ $isActive ? ' (atual)' : '' }}</h6>
                  <p class="text-gray ellipsis mb-0">{{ $locationShort ?: 'Clique para selecionar esta localidade' }}</p>
                </div>
              </button>
            </form>
            <div class="dropdown-divider"></div>
          @endforeach
          <h6 class="p-3 mb-0 text-center">Selecione uma localidade acima</h6>
        </div>
      </li>
      @endif
      @endauth
      @auth
      @php
        $navUserName  = Auth::user()->name;
        $navUserEmail = Auth::user()->email;
      @endphp
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link nav-profile-trigger dropdown-toggle"
           id="profileDropdown"
           href="#"
           data-bs-toggle="dropdown"
           aria-expanded="false"
           data-bs-popper="static">
          <i class="mdi mdi-account-circle-outline trigger-icon"></i>
          <span class="trigger-name">{{ $navUserName }}</span>
          <i class="mdi mdi-chevron-down trigger-chevron"></i>
        </a>

        <div class="dropdown-menu profile-dropdown-menu"
             aria-labelledby="profileDropdown"
             data-bs-popper="static">

          {{-- Cabeçalho --}}
          <div class="pd-header">
            <p class="pd-header-name">{{ $navUserName }}</p>
            <p class="pd-header-email">{{ $navUserEmail }}</p>
          </div>

          {{-- Ações --}}
          <div class="pd-items">
            <a href="{{ route('profile.show') }}" class="pd-item">
              <i class="mdi mdi-account-outline"></i>
              Meu Perfil
            </a>

            <a href="#"
               class="pd-item"
               data-bs-toggle="modal"
               data-bs-target="#profileChangePasswordModal">
              <i class="mdi mdi-lock-outline"></i>
              Alterar Senha
            </a>

            <div class="pd-divider"></div>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="pd-item pd-item--danger">
                <i class="mdi mdi-logout"></i>
                Sair
              </button>
            </form>
          </div>

        </div>
      </li>
      @endauth
     
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
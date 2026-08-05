{{-- Removido use DB - não é mais necessário, dados vêm da sessão --}}

{{-- Style inline: o navbar é @include após @stack('styles') no layout, então @push não funciona --}}
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

  .dropdown-menu.navbar-dropdown form {
    margin: 0;
    padding: 0;
  }

  .dropdown-menu.navbar-dropdown form button.dropdown-item {
    width: 100%;
    text-align: left;
  }

  .dropdown-menu.navbar-dropdown form button.dropdown-item.active {
    background-color: rgba(102, 126, 234, 0.1);
  }

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

  .navbar-brand-wrapper .navbar-brand,
  .navbar-brand-wrapper .navbar-brand:hover,
  .navbar-brand-wrapper .navbar-brand:focus,
  .navbar-brand-wrapper .navbar-brand:active {
    color: inherit !important;
  }

  .navbar-brand-wrapper .navbar-brand .tenant-name-text-only {
    color: #ffffff !important;
  }

  /* Desktop: logo completa (como antes) */
  .navbar .navbar-brand-wrapper .navbar-brand.brand-logo {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100%;
  }

  .navbar .navbar-brand-wrapper .navbar-brand.brand-logo .navbar-logo-main {
    max-height: 60px !important;
    max-width: 200px !important;
    height: 60px !important;
    width: auto !important;
    object-fit: contain !important;
    object-position: center !important;
    display: block !important;
    margin: 0 auto !important;
  }

  /* Mini logo: escondida no desktop (padrão do tema) */
  .navbar .navbar-brand-wrapper .navbar-brand.brand-logo-mini {
    display: none !important;
  }

  /* Mobile: esconde logo grande, mostra mini com área maior */
  @media (max-width: 991px) {
    .navbar .navbar-brand-wrapper {
      width: 160px !important;
      flex-shrink: 0;
      padding: 0 0.4rem;
    }

    .navbar .navbar-brand-wrapper .navbar-brand.brand-logo {
      display: none !important;
    }

    .navbar .navbar-brand-wrapper .navbar-brand.brand-logo-mini {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 100% !important;
      height: 100% !important;
      margin: 0 !important;
      padding: 0 0.35rem !important;
      line-height: 1 !important;
    }

    .navbar .navbar-brand-wrapper .navbar-brand.brand-logo-mini .navbar-logo-mobile {
      height: 38px !important;
      width: auto !important;
      max-width: 150px !important;
      object-fit: contain !important;
      object-position: center !important;
      display: block !important;
      margin: 0 auto !important;
    }
  }

  @media (max-width: 480px) {
    .navbar .navbar-brand-wrapper {
      width: 140px !important;
    }

    .navbar .navbar-brand-wrapper .navbar-brand.brand-logo-mini .navbar-logo-mobile {
      height: 34px !important;
      max-width: 128px !important;
    }
  }
</style>

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
    {{-- Desktop --}}
    <a class="navbar-brand brand-logo d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
      <img src="{{ asset('assets/images/visaosis_logo.svg') }}" alt="VisaoSis" class="navbar-logo-main" />
    </a>
    {{-- Mobile (tema esconde brand-logo e mostra brand-logo-mini abaixo de 991px) --}}
    <a class="navbar-brand brand-logo-mini d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}" title="VisaoSis">
      <img src="{{ asset('assets/images/visaosis_logo.svg') }}" alt="VisaoSis" class="navbar-logo-mobile" />
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
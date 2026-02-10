<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-category">Main</li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <span class="icon-bg"><i class="mdi mdi-cube menu-icon"></i></span>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <span class="icon-bg"><i class="mdi mdi-account-multiple menu-icon"></i></span>
        <span class="menu-title">Usuários</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#cadastros" aria-expanded="{{ request()->routeIs('pessoas.*') || request()->routeIs('profissionais.*') || request()->routeIs('categorias.*') || request()->routeIs('laboratorios.*') || request()->routeIs('produtos.*') ? 'true' : 'false' }}" aria-controls="cadastros">
        <span class="icon-bg"><i class="mdi mdi-file-document-edit-outline menu-icon"></i></span>
        <span class="menu-title">Cadastros</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ request()->routeIs('pessoas.*') || request()->routeIs('profissionais.*') || request()->routeIs('categorias.*') || request()->routeIs('laboratorios.*') || request()->routeIs('produtos.*') ? 'show' : '' }}" id="cadastros">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pessoas.*') ? 'active' : '' }}" href="{{ route('pessoas.index') }}">
              <i class="mdi mdi-account-heart-outline menu-icon me-2"></i>
              Pacientes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profissionais.*') ? 'active' : '' }}" href="{{ route('profissionais.index') }}">
              <i class="mdi mdi-account-tie menu-icon me-2"></i>
              Profissionais
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
              <i class="mdi mdi-tag-multiple menu-icon me-2"></i>
              Categorias
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('laboratorios.*') ? 'active' : '' }}" href="{{ route('laboratorios.index') }}">
              <i class="mdi mdi-flask-outline menu-icon me-2"></i>
              Laboratórios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('produtos.*') ? 'active' : '' }}" href="{{ route('produtos.index') }}">
              <i class="mdi mdi-package-variant menu-icon me-2"></i>
              Produtos
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <span class="icon-bg"><i class="mdi mdi-crosshairs-gps menu-icon"></i></span>
        <span class="menu-title">UI Elements</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="#">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="#">Dropdowns</a></li>
          <li class="nav-item"> <a class="nav-link" href="#">Typography</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <span class="icon-bg"><i class="mdi mdi-contacts menu-icon"></i></span>
        <span class="menu-title">Icons</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <span class="icon-bg"><i class="mdi mdi-format-list-bulleted menu-icon"></i></span>
        <span class="menu-title">Forms</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <span class="icon-bg"><i class="mdi mdi-chart-bar menu-icon"></i></span>
        <span class="menu-title">Charts</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">
        <span class="icon-bg"><i class="mdi mdi-table-large menu-icon"></i></span>
        <span class="menu-title">Tables</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
        <span class="icon-bg"><i class="mdi mdi-lock menu-icon"></i></span>
        <span class="menu-title">User Pages</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="auth">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="#"> Blank Page </a></li>
          @guest
          @if(Route::has('login'))
          <li class="nav-item"> <a class="nav-link" href="{{ route('login') }}"> Login </a></li>
          @endif
          @if(Route::has('register'))
          <li class="nav-item"> <a class="nav-link" href="{{ route('register') }}"> Register </a></li>
          @endif
          @endguest
          <li class="nav-item"> <a class="nav-link" href="#"> 404 </a></li>
          <li class="nav-item"> <a class="nav-link" href="#"> 500 </a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item documentation-link">
      <a class="nav-link" href="http://www.bootstrapdash.com/demo/connect-plus-free/jquery/documentation/documentation.html" target="_blank">
        <span class="icon-bg">
          <i class="mdi mdi-file-document-box menu-icon"></i>
        </span>
        <span class="menu-title">Documentation</span>
      </a>
    </li>
    @auth
    <li class="nav-item sidebar-user-actions">
      <div class="user-details">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="d-flex align-items-center">
              <div class="sidebar-profile-img">
                <img src="{{ asset('assets/images/faces/face28.png') }}" alt="image">
              </div>
              <div class="sidebar-profile-text">
                <p class="mb-1">@auth{{ Auth::user()->name }}@else User @endauth</p>
              </div>
            </div>
          </div>
          <div class="badge badge-danger">3</div>
        </div>
      </div>
    </li>
    <li class="nav-item sidebar-user-actions">
      <div class="sidebar-user-menu">
        <a href="#" class="nav-link"><i class="mdi mdi-settings menu-icon"></i>
          <span class="menu-title">Settings</span>
        </a>
      </div>
    </li>
    <li class="nav-item sidebar-user-actions">
      <div class="sidebar-user-menu">
        <a href="#" class="nav-link"><i class="mdi mdi-speedometer menu-icon"></i>
          <span class="menu-title">Take Tour</span></a>
      </div>
    </li>
    <li class="nav-item sidebar-user-actions">
      <div class="sidebar-user-menu">
        @if(Route::has('logout'))
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
            <i class="mdi mdi-logout menu-icon"></i>
            <span class="menu-title">Log Out</span>
          </button>
        </form>
        @else
        <a href="#" class="nav-link">
          <i class="mdi mdi-logout menu-icon"></i>
          <span class="menu-title">Log Out</span>
        </a>
        @endif
      </div>
    </li>
    @endauth
  </ul>
</nav>
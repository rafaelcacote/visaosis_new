<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Connect Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #ffffff;
            -webkit-text-size-adjust: 100%;
        }
        
        .login-container {
            display: flex;
            height: 100vh;
            height: 100dvh;
            width: 100%;
            max-width: 100vw;
        }

        /* Seção da Imagem (Lado Esquerdo) */
        .image-section {
            flex: 2;
            position: relative;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%), url('{{ asset('img/login_imagem.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .image-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        
        .image-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 2rem;
        }
        
        .logo-section {
            margin-bottom: 3rem;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .brand-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            color: white;
        }
        
        .brand-subtitle {
            font-size: 1.2rem;
            opacity: 0.95;
            font-weight: 300;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
            color: white;
        }
        
        .welcome-text {
            font-size: 1.1rem;
            margin-top: 2rem;
            opacity: 0.9;
            line-height: 1.6;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
            color: white;
        }
        
        /* Seção do Login (Lado Direito) */
        .login-section {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }
        
        .login-form-container {
            width: 100%;
            max-width: 400px;
            margin-top: -2rem;
        }
        
        .signin-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .signin-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .signin-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #fafafa;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .login-button {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
        }
        
        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .button-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Loading elegante para busca de usuário */
        .user-loading {
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease-in-out;
            margin-bottom: 1.5rem;
        }
        .user-loading.show {
            opacity: 1;
            transform: translateY(0);
        }
        .user-loading-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        .user-loading-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        .loading-dots {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .loading-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            animation: bounce 1.4s ease-in-out infinite both;
        }
        .loading-dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dot:nth-child(2) { animation-delay: -0.16s; }
        .loading-dot:nth-child(3) { animation-delay: 0s; }
        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
        .loading-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        /* User Info Card */
        .user-info-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .user-info-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }
        
        .user-details h4 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .user-details p {
            margin: 0;
            font-size: 0.8rem;
            color: #6b7280;
            line-height: 1.3;
        }
        
        .clear-user-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .clear-user-btn:hover {
            color: #6b7280;
            background: rgba(0, 0, 0, 0.05);
        }
        
        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }
        
        .alert-error {
            background: #fef2f2;
            border-left-color: #ef4444;
            color: #dc2626;
        }
        
        .alert-error i {
            margin-right: 0.5rem;
        }
        
        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.85rem;
        }
        
        /* Responsividade */
        @media (max-width: 991px) {
            body {
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .login-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
            }

            /* Hero lateral vira faixa compacta no topo */
            .image-section {
                flex: 0 0 auto;
                min-height: 0;
                height: auto;
                padding: 1.25rem 1rem 1.5rem;
                align-items: flex-start;
                justify-content: center;
            }

            .image-content {
                padding: 0;
                width: 100%;
                text-align: left;
            }

            .logo-section {
                margin-bottom: 0;
                display: flex;
                align-items: center;
                gap: 0.85rem;
            }

            .logo-icon {
                width: 48px;
                height: 48px;
                border-radius: 12px;
                margin: 0;
                flex-shrink: 0;
            }

            .logo-icon i {
                font-size: 1.35rem;
            }

            .logo-text {
                text-align: left;
            }

            .logo-section .brand-title {
                font-size: 1.45rem;
                margin-bottom: 0.1rem;
                line-height: 1.15;
            }

            .logo-section .brand-subtitle {
                font-size: 0.85rem;
                margin: 0;
                line-height: 1.2;
            }

            .welcome-text {
                display: none;
            }

            .login-section {
                flex: 1 1 auto;
                padding: 1.5rem 1.25rem calc(1.5rem + env(safe-area-inset-bottom, 0px));
                align-items: flex-start;
                justify-content: flex-start;
            }

            .login-form-container {
                margin-top: 0;
                max-width: 100%;
            }

            .signin-header {
                margin-bottom: 1.5rem;
                text-align: left;
            }

            .signin-title {
                font-size: 1.4rem;
            }

            .signin-subtitle {
                font-size: 0.9rem;
            }

            .form-group {
                margin-bottom: 1.15rem;
            }

            /* 16px evita zoom automático no iOS */
            .form-input {
                font-size: 16px;
                padding: 0.95rem 1rem;
                border-radius: 10px;
            }

            .login-button {
                padding: 1rem;
                font-size: 1rem;
                border-radius: 10px;
                min-height: 48px;
            }

            .login-footer {
                margin-top: 1.5rem;
                padding-top: 1.15rem;
                font-size: 0.8rem;
            }

            .user-info-card,
            .user-loading-card {
                border-radius: 10px;
            }

            .user-info-content {
                align-items: flex-start;
                padding-right: 1.25rem;
            }

            .user-details h4 {
                font-size: 0.9rem;
                word-break: break-word;
            }

            .user-details p {
                font-size: 0.78rem;
                word-break: break-word;
            }
        }

        @media (max-width: 480px) {
            .image-section {
                padding: 1rem 1rem 1.15rem;
            }

            .logo-icon {
                width: 42px;
                height: 42px;
                border-radius: 10px;
            }

            .logo-icon i {
                font-size: 1.15rem;
            }

            .logo-section .brand-title {
                font-size: 1.3rem;
            }

            .logo-section .brand-subtitle {
                font-size: 0.8rem;
            }

            .login-section {
                padding: 1.25rem 1rem calc(1.25rem + env(safe-area-inset-bottom, 0px));
            }

            .signin-title {
                font-size: 1.25rem;
            }

            .signin-subtitle {
                font-size: 0.85rem;
            }

            .form-label {
                font-size: 0.85rem;
            }
        }

        /* Telas bem baixas (landscape / teclado aberto) */
        @media (max-width: 991px) and (max-height: 560px) {
            .image-section {
                padding: 0.75rem 1rem;
            }

            .logo-icon {
                display: none;
            }

            .logo-section .brand-title {
                font-size: 1.15rem;
            }

            .login-section {
                padding-top: 1rem;
            }

            .signin-header {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Seção da Imagem (Lado Esquerdo) -->
        <div class="image-section">
            <div class="image-overlay"></div>
            <div class="image-content">
                <div class="logo-section">
                    <div class="logo-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="logo-text">
                        <h1 class="brand-title">VisaoSis</h1>
                        <p class="brand-subtitle">Sistema de Gestão</p>
                    </div>
                </div>
                <div class="welcome-text">
                    <p>Bem-vindo ao sistema de gestão mais completo.</p>
                    <p>Gerencie seus dados de forma eficiente.</p>
                </div>
            </div>
        </div>

        <!-- Seção do Login (Lado Direito) -->
        <div class="login-section">
            <div class="login-form-container">
                <!-- Header -->
                <div class="signin-header">
                    <h2 class="signin-title">Entre na sua conta</h2>
                    <p class="signin-subtitle">Digite suas credenciais para acessar o sistema</p>
                </div>

                <!-- Alerts -->
                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <div style="margin-bottom: 0.5rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Erro na autenticação:</strong>
                        </div>
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Loading Card -->
                <div id="user-loading" class="user-loading">
                    <div class="user-loading-card">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="flex-shrink: 0;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;" class="loading-pulse">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Buscando usuário</span>
                                    <div class="loading-dots">
                                        <div class="loading-dot"></div>
                                        <div class="loading-dot"></div>
                                        <div class="loading-dot"></div>
                                    </div>
                                </div>
                                <p style="color: #6b7280; font-size: 0.8rem; margin: 0;">Verificando informações no sistema...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info Card -->
                <div id="user-info" class="user-info-card hidden">
                    <button type="button" id="clear-user-info" class="clear-user-btn">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="user-info-content">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h4 id="user-name">Nome do Usuário</h4>
                            <p id="user-email">email@exemplo.com</p>
                            <p id="user-role">Cargo/Função</p>
                            <div style="display: flex; gap: 1rem; margin-top: 0.25rem;">
                                <span style="font-size: 0.75rem; color: #6b7280;">
                                    <i class="fas fa-building" style="margin-right: 0.25rem;"></i>
                                    <span id="tenant-name">Empresa</span>
                                </span>
                                <span style="font-size: 0.75rem; color: #6b7280;">
                                    <i class="fas fa-map-marker-alt" style="margin-right: 0.25rem;"></i>
                                    <span id="location-name">Localização</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" id="login-form">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="form-input @error('email') border-red-500 @enderror" 
                            placeholder="Digite seu email"
                            required 
                            autofocus
                        >
                        @error('email')
                            <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input @error('password') border-red-500 @enderror" 
                            placeholder="Digite sua senha"
                            required
                        >
                        @error('password')
                            <p style="color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit" 
                        id="login-button"
                        class="login-button"
                    >
                        <div id="button-text" class="button-content">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Entrar</span>
                        </div>
                        <div id="button-loading" style="display: none; position: absolute; inset: 0; align-items: center; justify-content: center;">
                            <div class="loading-spinner"></div>
                        </div>
                    </button>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    <p>Autenticação fornecida pelo <strong>Cerberus</strong></p>
                    <p>Sistema de autenticação centralizado</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const userInfoDiv = document.getElementById('user-info');
            const userLoadingDiv = document.getElementById('user-loading');
            const userName = document.getElementById('user-name');
            const userEmail = document.getElementById('user-email');
            const userRole = document.getElementById('user-role');
            const tenantName = document.getElementById('tenant-name');
            const locationName = document.getElementById('location-name');
            const clearUserInfoBtn = document.getElementById('clear-user-info');
            const loginForm = document.getElementById('login-form');
            const loginButton = document.getElementById('login-button');
            const buttonText = document.getElementById('button-text');
            const buttonLoading = document.getElementById('button-loading');
            
            let userInfoTimeout;
            let isSearching = false;

            // Auto-focus
            if (!emailField.value) {
                emailField.focus();
            } else {
                passwordField.focus();
            }

            // Loading no botão
            loginForm.addEventListener('submit', function() {
                buttonText.classList.add('hidden');
                buttonLoading.classList.remove('hidden');
                loginButton.disabled = true;
                loginButton.classList.add('opacity-75', 'cursor-not-allowed');
            });

            // Mostrar loading
            function showLoading() {
                if (isSearching) return;
                isSearching = true;
                userLoadingDiv.classList.add('show');
                userInfoDiv.classList.add('hidden');
            }

            // Esconder loading
            function hideLoading() {
                isSearching = false;
                userLoadingDiv.classList.remove('show');
            }

            // Buscar informações do usuário
            function fetchUserInfo(email) {
                console.log('fetchUserInfo chamado com email:', email);
                
                if (!email || !email.includes('@')) {
                    console.log('Email inválido, ocultando informações');
                    hideUserInfo();
                    hideLoading();
                    return;
                }

                console.log('Email válido, iniciando busca...');

                if (userInfoTimeout) {
                    clearTimeout(userInfoTimeout);
                }

                // Mostrar loading imediatamente
                showLoading();

                userInfoTimeout = setTimeout(() => {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    console.log('CSRF Token encontrado:', csrfToken ? 'Sim' : 'Não');
                    
                    if (!csrfToken) {
                        console.error('CSRF Token não encontrado!');
                        hideUserInfo();
                        hideLoading();
                        return;
                    }
                    
                    const url = '/api/user/info';
                    console.log('Fazendo requisição para:', url);
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(response => {
                        console.log('Resposta recebida:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Dados recebidos:', data);
                        hideLoading(); // Esconder loading sempre
                        
                        if (data.success && data.user) {
                            showUserInfo(data.user);
                        } else {
                            console.log('Resposta não contém usuário válido');
                            hideUserInfo();
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar informações do usuário:', error);
                        hideLoading();
                        hideUserInfo();
                    });
                }, 500);
            }

            // Mostrar informações do usuário
            function showUserInfo(user) {
                console.log('Mostrando usuário:', user); // Debug
                userName.textContent = user.name || 'Nome não informado';
                userEmail.textContent = user.email || '';
                userRole.textContent = user.role || user.position || 'Cargo não informado';
                
                if (user.tenant) {
                    tenantName.textContent = user.tenant.name || user.tenant.trade_name || 'Empresa não informada';
                } else {
                    tenantName.textContent = 'Empresa não informada';
                }
                
                if (user.location) {
                    locationName.textContent = user.location.name || user.location.short_name || 'Localização não informada';
                } else {
                    locationName.textContent = 'Localização não informada';
                }
                
                userInfoDiv.classList.remove('hidden');
            }

            // Ocultar informações do usuário
            function hideUserInfo() {
                userInfoDiv.classList.add('hidden');
                hideLoading();
            }

            // Event listeners
            emailField.addEventListener('input', function() {
                const email = this.value.trim();
                console.log('Evento input disparado, email:', email);
                fetchUserInfo(email);
            });

            clearUserInfoBtn.addEventListener('click', function() {
                hideUserInfo();
                hideLoading();
                emailField.value = '';
                emailField.focus();
            });

            // Se já houver email preenchido
            if (emailField.value) {
                fetchUserInfo(emailField.value);
            }
        });
    </script>
</body>
</html>
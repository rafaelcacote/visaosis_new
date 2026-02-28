<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - Menu e Sessão</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 0;
            border-bottom: 2px solid #28a745;
            padding-bottom: 8px;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status.ok {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .status.warning {
            background: #fff3cd;
            color: #856404;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border-left: 4px solid #007bff;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }
        .card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #6c757d;
        }
        .card h3 {
            margin-top: 0;
            color: #495057;
        }
        .menu-item {
            background: #e9ecef;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        .menu-item.child {
            margin-left: 20px;
            border-left-color: #28a745;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            background: #007bff;
            color: white;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 5px;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug - Menu e Sessão do Cerberus</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="{{ route('dashboard') }}" class="btn">← Voltar ao Dashboard</a>
            <a href="{{ route('debug.menu') }}" class="btn">🔄 Recarregar</a>
        </div>

        @php
            $authCheck = Auth::check();
            $hasToken = session()->has('cerberusToken');
            $hasItems = session()->has('items');
            $itemsCount = count(session('items', []));
            $items = session('items', []);
            $menuItems = \App\Helpers\MenuHelper::getMenuItems('left_sidebar');
        @endphp

        {{-- Status Geral --}}
        <div class="section">
            <h2>📊 Status Geral</h2>
            <div class="grid">
                <div class="card">
                    <h3>Autenticação Laravel</h3>
                    <p>
                        Status: 
                        <span class="status {{ $authCheck ? 'ok' : 'error' }}">
                            {{ $authCheck ? '✓ Autenticado' : '✗ Não Autenticado' }}
                        </span>
                    </p>
                    @if($authCheck)
                        <p><strong>ID:</strong> {{ Auth::id() }}</p>
                        <p><strong>Nome:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    @endif
                </div>

                <div class="card">
                    <h3>Token Cerberus</h3>
                    <p>
                        Status: 
                        <span class="status {{ $hasToken ? 'ok' : 'error' }}">
                            {{ $hasToken ? '✓ Token Presente' : '✗ Token Ausente' }}
                        </span>
                    </p>
                    @if($hasToken)
                        <p><strong>Token:</strong> {{ substr(session('cerberusToken'), 0, 30) }}...</p>
                    @endif
                </div>

                <div class="card">
                    <h3>Items/Permissões</h3>
                    <p>
                        Status: 
                        <span class="status {{ $itemsCount > 0 ? 'ok' : 'error' }}">
                            {{ $itemsCount > 0 ? "✓ $itemsCount items" : '✗ Nenhum item' }}
                        </span>
                    </p>
                </div>

                <div class="card">
                    <h3>Menu Processado</h3>
                    <p>
                        Status: 
                        <span class="status {{ count($menuItems) > 0 ? 'ok' : 'warning' }}">
                            {{ count($menuItems) > 0 ? count($menuItems) . ' items no menu' : '⚠ Menu vazio' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Diagnóstico --}}
        @if(!$authCheck)
            <div class="alert alert-danger">
                <strong>❌ Erro:</strong> Usuário não está autenticado. Faça login primeiro.
            </div>
        @elseif(!$hasToken)
            <div class="alert alert-danger">
                <strong>❌ Erro:</strong> Token do Cerberus não encontrado na sessão. O login pode ter falho.
            </div>
        @elseif($itemsCount === 0)
            <div class="alert alert-warning">
                <strong>⚠ Atenção:</strong> Nenhum item/permissão foi retornado pelo Cerberus. 
                Possíveis causas:
                <ul>
                    <li>Usuário não tem perfil atribuído no Cerberus</li>
                    <li>Perfil não tem items atribuídos</li>
                    <li>Items não estão com status ativo</li>
                    <li>Items não têm show_menu = true</li>
                </ul>
            </div>
        @elseif(count($menuItems) === 0)
            <div class="alert alert-warning">
                <strong>⚠ Atenção:</strong> Items foram recebidos ({{ $itemsCount }}), mas nenhum foi filtrado para o menu.
                Possíveis causas:
                <ul>
                    <li>Items não têm type_menu = 'left_sidebar'</li>
                    <li>Items não têm show_menu = true</li>
                </ul>
            </div>
        @else
            <div class="alert alert-success">
                <strong>✓ Sucesso:</strong> Sistema funcionando corretamente! 
                {{ $itemsCount }} items recebidos, {{ count($menuItems) }} no menu.
            </div>
        @endif

        {{-- Items da Sessão (Raw) --}}
        <div class="section">
            <h2>📦 Items da Sessão (Raw do Cerberus)</h2>
            <p><strong>Total:</strong> {{ $itemsCount }} items</p>
            
            @if($itemsCount > 0)
                <pre>{{ json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <div class="alert alert-warning">Nenhum item na sessão</div>
            @endif
        </div>

        {{-- Menu Processado --}}
        <div class="section">
            <h2>🎨 Menu Processado (MenuHelper)</h2>
            <p><strong>Total:</strong> {{ count($menuItems) }} items no menu</p>
            
            @if(count($menuItems) > 0)
                @foreach($menuItems as $item)
                    <div class="menu-item">
                        <strong>{{ $item['name'] ?? $item['short_name'] ?? 'Sem nome' }}</strong>
                        <span class="badge">{{ $item['type_menu'] ?? 'N/A' }}</span>
                        @if(isset($item['show_menu']))
                            <span class="badge" style="background: {{ $item['show_menu'] ? '#28a745' : '#dc3545' }}">
                                {{ $item['show_menu'] ? 'Visível' : 'Oculto' }}
                            </span>
                        @endif
                        <br>
                        <small>
                            <strong>URL:</strong> {{ $item['url'] ?? '#' }} | 
                            <strong>Icon:</strong> {{ $item['icon'] ?? 'N/A' }} |
                            <strong>Order:</strong> {{ $item['ordering'] ?? 'N/A' }}
                        </small>
                        
                        @if(!empty($item['children']))
                            <div style="margin-top: 10px;">
                                <strong>Filhos ({{ count($item['children']) }}):</strong>
                                @foreach($item['children'] as $child)
                                    <div class="menu-item child">
                                        {{ $child['name'] ?? $child['short_name'] ?? 'Sem nome' }}
                                        <span class="badge">{{ $child['type_menu'] ?? 'N/A' }}</span>
                                        <br>
                                        <small>
                                            <strong>URL:</strong> {{ $child['url'] ?? '#' }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="alert alert-warning">Nenhum item no menu processado</div>
            @endif
        </div>

        {{-- Perfis --}}
        <div class="section">
            <h2>👤 Perfis do Usuário</h2>
            @php $perfis = session('perfis', []); @endphp
            <p><strong>Total:</strong> {{ count($perfis) }} perfis</p>
            
            @if(count($perfis) > 0)
                <pre>{{ json_encode($perfis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <div class="alert alert-warning">Nenhum perfil encontrado</div>
            @endif
        </div>

        {{-- Tenant/Location --}}
        <div class="section">
            <h2>🏢 Tenant e Location</h2>
            <div class="grid">
                <div class="card">
                    <h3>Tenant</h3>
                    @if(session('tenant'))
                        <p><strong>ID:</strong> {{ session('tenant_id') }}</p>
                        <p><strong>Nome:</strong> {{ session('tenant')->name ?? 'N/A' }}</p>
                        @if(isset(session('tenant')->logo_path))
                            <p><strong>Logo:</strong> {{ session('tenant')->logo_path }}</p>
                        @endif
                    @else
                        <p class="status error">Não configurado</p>
                    @endif
                </div>

                <div class="card">
                    <h3>Location</h3>
                    @if(session('location'))
                        <p><strong>ID:</strong> {{ session('location_id') }}</p>
                        <p><strong>Nome:</strong> {{ session('location')->name ?? 'N/A' }}</p>
                    @else
                        <p class="status error">Não configurado</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sessão Completa --}}
        <div class="section">
            <h2>🔐 Sessão Completa (Debug)</h2>
            <pre>{{ json_encode(session()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>

        {{-- Instruções --}}
        <div class="section">
            <h2>📝 Como Resolver Problemas</h2>
            
            <h3>Se não há items na sessão:</h3>
            <ol>
                <li>Verifique os logs em <code>storage/logs/laravel.log</code></li>
                <li>Procure por <code>=== DADOS RETORNADOS DO CERBERUS ===</code></li>
                <li>Verifique se o Cerberus está retornando items na resposta da API</li>
                <li>No Cerberus, verifique se o usuário tem perfil atribuído</li>
                <li>No Cerberus, verifique se o perfil tem items atribuídos</li>
            </ol>

            <h3>Se há items mas o menu está vazio:</h3>
            <ol>
                <li>Verifique se os items têm <code>type_menu = 'left_sidebar'</code></li>
                <li>Verifique se os items têm <code>show_menu = true</code></li>
                <li>Verifique se os items estão com <code>status = 1</code> (ativo) no Cerberus</li>
            </ol>

            <h3>Verificar no Cerberus:</h3>
            <ol>
                <li>Acesse o Cerberus</li>
                <li>Vá em Usuários → Editar o usuário logado</li>
                <li>Verifique a aba "Perfis" - deve ter pelo menos 1 perfil</li>
                <li>Vá em Perfis → Editar o perfil</li>
                <li>Verifique a aba "Items" - deve ter items atribuídos</li>
                <li>Vá em Items → Verificar cada item:
                    <ul>
                        <li>Status = Ativo</li>
                        <li>Show Menu = Sim</li>
                        <li>Type Menu = left_sidebar (para menu lateral)</li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>
</body>
</html>

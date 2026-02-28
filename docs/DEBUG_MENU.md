# 🔍 Debug do Menu Dinâmico

## Como Usar

### 1. Faça Login no Sistema
Acesse o VisaoSis e faça login normalmente.

### 2. Acesse a Página de Debug

**Opção 1 - Visual (Recomendado):**
```
http://localhost:8001/debug/menu
```

**Opção 2 - JSON (para APIs):**
```
http://localhost:8001/debug/menu/json
```

### 3. Analise as Informações

A página de debug mostra:

#### ✅ Status Geral
- **Autenticação Laravel:** Se você está logado
- **Token Cerberus:** Se o token foi recebido
- **Items/Permissões:** Quantos items foram recebidos do Cerberus
- **Menu Processado:** Quantos items aparecem no menu

#### 📦 Items da Sessão (Raw)
Mostra EXATAMENTE o que o Cerberus retornou na API.

Se estiver **vazio**, o problema está no Cerberus:
- Usuário não tem perfil
- Perfil não tem items
- Items estão inativos

#### 🎨 Menu Processado
Mostra os items DEPOIS do filtro do MenuHelper.

Se **items da sessão tem dados** mas **menu processado está vazio**, o problema é:
- Items não têm `type_menu = 'left_sidebar'`
- Items não têm `show_menu = true`

## 📋 Logs no Arquivo

Além da página de debug, o sistema grava logs em:
```
storage/logs/laravel.log
```

### Procure por estas seções:

#### 1. Dados do Cerberus (no login)
```
=== DADOS RETORNADOS DO CERBERUS ===
```

Mostra o que o Cerberus retornou na API de login.

#### 2. Dados na Sessão (no login)
```
=== DADOS SALVOS NA SESSÃO ===
```

Mostra o que foi salvo na sessão do Laravel.

#### 3. Sidebar Debug (ao carregar página)
```
=== SIDEBAR DEBUG ===
```

Mostra o que o sidebar está recebendo ao renderizar.

## 🔧 Problemas Comuns

### Problema 1: Items da sessão está vazio

**Sintoma:**
```json
{
  "items_count": 0,
  "items_raw": []
}
```

**Causa:** Cerberus não está retornando items.

**Solução:**

1. Verifique no Cerberus se o usuário tem perfil:
   - Usuários → Editar usuário → Aba "Perfis"
   - Deve ter pelo menos 1 perfil ativo

2. Verifique se o perfil tem items:
   - Perfis → Editar perfil → Aba "Items"
   - Deve ter items atribuídos

3. Verifique se os items estão ativos:
   - Items → Listar
   - Status deve ser "Ativo"

4. Verifique os logs:
   ```bash
   tail -f storage/logs/laravel.log | grep "CERBERUS"
   ```

### Problema 2: Tem items mas menu está vazio

**Sintoma:**
```json
{
  "items_count": 10,
  "menu_items_count": 0
}
```

**Causa:** Items não estão configurados corretamente para o menu.

**Solução:**

1. Verifique no Cerberus cada item:
   - **Type Menu:** Deve ser `left_sidebar` (para menu lateral)
   - **Show Menu:** Deve estar marcado (true)
   - **Status:** Deve estar ativo

2. Exemplo de item correto no Cerberus:
   ```
   Nome: Dashboard
   URL: /dashboard
   Icon: mdi mdi-view-dashboard
   Type Menu: left_sidebar  ← IMPORTANTE
   Show Menu: ✓ Sim        ← IMPORTANTE
   Status: ✓ Ativo         ← IMPORTANTE
   Ordering: 1
   ```

### Problema 3: Menu aparece mas não tem ícones

**Causa:** Campo `icon` está vazio ou com valor inválido.

**Solução:**

Use ícones do Material Design Icons (MDI):
```
mdi mdi-view-dashboard
mdi mdi-account-multiple
mdi mdi-hospital-building
mdi mdi-stethoscope
mdi mdi-calendar-check
```

Lista completa: https://materialdesignicons.com/

### Problema 4: Menu aparece mas ordem está errada

**Causa:** Campo `ordering` não está configurado.

**Solução:**

No Cerberus, defina o campo `ordering`:
```
Dashboard → ordering: 1
Pacientes → ordering: 2
Recepção → ordering: 3
```

## 🧪 Testando Passo a Passo

### Teste 1: Verificar Autenticação
```
1. Acesse: http://localhost:8001/debug/menu
2. Verifique: "Autenticação Laravel" deve estar ✓ Autenticado
3. Se não: Faça logout e login novamente
```

### Teste 2: Verificar Token
```
1. Na página de debug, verifique: "Token Cerberus"
2. Deve estar: ✓ Token Presente
3. Se não: 
   - Verifique se o Cerberus está rodando
   - Verifique CERBERUS_URL no .env
   - Verifique logs de erro no login
```

### Teste 3: Verificar Items
```
1. Na página de debug, verifique: "Items/Permissões"
2. Deve mostrar: ✓ X items (onde X > 0)
3. Se 0:
   - Vá para o Cerberus
   - Verifique se usuário tem perfil
   - Verifique se perfil tem items
```

### Teste 4: Verificar Menu
```
1. Na página de debug, verifique: "Menu Processado"
2. Deve mostrar: ✓ X items no menu (onde X > 0)
3. Se 0 mas tem items:
   - Verifique type_menu dos items
   - Verifique show_menu dos items
```

## 📊 Exemplo de Debug Bem-Sucedido

```json
{
  "auth_check": true,
  "user_id": 1,
  "user_name": "Administrador",
  "session_data": {
    "has_cerberus_token": true,
    "items_count": 8,
    "items_raw": [
      {
        "id": 1,
        "name": "Dashboard",
        "url": "/dashboard",
        "icon": "mdi mdi-view-dashboard",
        "type_menu": "left_sidebar",
        "show_menu": true,
        "ordering": 1,
        "children": []
      },
      {
        "id": 2,
        "name": "Pacientes",
        "url": "/pessoas",
        "icon": "mdi mdi-account-multiple",
        "type_menu": "left_sidebar",
        "show_menu": true,
        "ordering": 2,
        "children": [...]
      }
    ]
  },
  "menu_helper": {
    "left_sidebar_items": [
      { "name": "Dashboard", ... },
      { "name": "Pacientes", ... }
    ]
  }
}
```

## 🎯 Checklist de Verificação

No Cerberus:

- [ ] Sistema "visaosis" está cadastrado e ativo
- [ ] Usuário tem pelo menos 1 perfil atribuído
- [ ] Perfil está ativo
- [ ] Perfil tem items atribuídos
- [ ] Items estão ativos (status = 1)
- [ ] Items têm `show_menu = true`
- [ ] Items têm `type_menu = 'left_sidebar'`
- [ ] Items têm `url` configurada
- [ ] Items têm `icon` configurado (opcional mas recomendado)
- [ ] Items têm `ordering` configurado (opcional mas recomendado)

No VisaoSis:

- [ ] .env tem CERBERUS_URL correto
- [ ] .env tem CERBERUS_SYSTEM_KEY = visaosis
- [ ] Usuário consegue fazer login
- [ ] Token é salvo na sessão
- [ ] Items são salvos na sessão
- [ ] Menu aparece no sidebar

## 🆘 Ainda com Problemas?

1. **Limpe o cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. **Verifique os logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Faça logout e login novamente**

4. **Acesse a página de debug:**
   ```
   http://localhost:8001/debug/menu
   ```

5. **Tire um print da página de debug** e analise os dados

## 📞 Informações para Suporte

Se precisar de ajuda, forneça:

1. Print da página `/debug/menu`
2. Logs do arquivo `storage/logs/laravel.log` (últimas 100 linhas)
3. Print da tela de edição do perfil no Cerberus (aba Items)
4. Print da tela de edição de um item no Cerberus

---

**Desenvolvido por:** IA Assistant
**Data:** 2026-02-12

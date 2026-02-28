# 🎨 Ícones do Menu - Como Funciona

## Resumo

**Os ícones vêm do Cerberus**, mas **o pacote de ícones precisa estar instalado neste projeto**.

---

## 📦 Pacote de Ícones Instalado

### Material Design Icons (MDI) v3.7.95

**Localização:**
- CSS: `public/assets/vendors/mdi/css/materialdesignicons.min.css`
- Fontes: `public/assets/vendors/mdi/fonts/`

**Carregado em:**
- `resources/views/layouts/app.blade.php` (linha 10)
  ```html
  <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  ```

---

## 🔄 Como Funciona

### 1. **Cerberus envia o nome do ícone**
No cadastro de cada item no Cerberus, você configura o campo `icon` com o nome da classe MDI.

**Exemplo no Cerberus:**
```
Nome: Dashboard
Icon: mdi mdi-view-dashboard
```

### 2. **O projeto renderiza o ícone**
O código em `resources/views/components/sidebar.blade.php` usa o valor que vem do Cerberus:

```php
<i class="{{ $item['icon'] ?? 'mdi mdi-circle' }} menu-icon"></i>
```

**Se o Cerberus enviar:** `mdi mdi-view-dashboard`  
**O HTML gerado será:** `<i class="mdi mdi-view-dashboard menu-icon"></i>`

---

## ✅ Checklist

### No Cerberus:
- [ ] Campo `icon` deve conter uma classe MDI válida
- [ ] Formato: `mdi mdi-nome-do-icone`
- [ ] Exemplo: `mdi mdi-view-dashboard`

### Neste Projeto:
- [x] ✅ Material Design Icons está instalado
- [x] ✅ CSS está sendo carregado no layout
- [x] ✅ Fontes estão na pasta correta

---

## 📋 Exemplos de Ícones MDI Válidos

Use estes formatos no campo `icon` do Cerberus:

```
mdi mdi-view-dashboard
mdi mdi-account-multiple
mdi mdi-hospital-building
mdi mdi-stethoscope
mdi mdi-calendar-check
mdi mdi-file-document
mdi mdi-settings
mdi mdi-logout
mdi mdi-menu
mdi mdi-home
```

**Lista completa de ícones disponíveis:**
- Site oficial: https://materialdesignicons.com/
- Versão instalada: **3.7.95**

---

## ⚠️ Importante

### 1. **O pacote DEVE estar instalado neste projeto**
Se você remover os arquivos do MDI (`public/assets/vendors/mdi/`), os ícones **não aparecerão**, mesmo que o Cerberus envie os nomes corretos.

### 2. **O valor do Cerberus deve ser uma classe MDI válida**
Se você colocar um nome de ícone que não existe no MDI (ex: `mdi mdi-icone-inexistente`), o ícone não aparecerá.

### 3. **Formato obrigatório**
O campo `icon` no Cerberus deve seguir o padrão:
```
mdi mdi-nome-do-icone
```

**❌ Errado:**
- `view-dashboard` (falta o prefixo `mdi`)
- `mdi-view-dashboard` (falta o primeiro `mdi`)
- `icon-dashboard` (não é MDI)

**✅ Correto:**
- `mdi mdi-view-dashboard`
- `mdi mdi-account`

---

## 🔍 Como Verificar se um Ícone Existe

### Opção 1: Site Oficial
1. Acesse: https://materialdesignicons.com/
2. Digite o nome do ícone na busca
3. Se encontrar, use o formato: `mdi mdi-nome-encontrado`

### Opção 2: Inspecionar o CSS
Abra o arquivo:
```
public/assets/vendors/mdi/css/materialdesignicons.min.css
```

Procure por `.mdi-nome-do-icone` - se existir, o ícone está disponível.

---

## 🛠️ Troubleshooting

### Problema: Ícone não aparece

**Causa 1:** Nome do ícone incorreto no Cerberus
- ✅ Verifique se está no formato `mdi mdi-nome-do-icone`
- ✅ Confirme que o ícone existe no MDI v3.7.95

**Causa 2:** CSS não está carregando
- ✅ Verifique o console do navegador (F12) para erros 404
- ✅ Confirme que o arquivo existe: `public/assets/vendors/mdi/css/materialdesignicons.min.css`
- ✅ Verifique se o link está no `app.blade.php`

**Causa 3:** Fontes não encontradas
- ✅ Verifique se os arquivos de fonte existem em `public/assets/vendors/mdi/fonts/`
- ✅ Arquivos necessários:
  - `materialdesignicons-webfont.eot`
  - `materialdesignicons-webfont.woff2`
  - `materialdesignicons-webfont.woff`
  - `materialdesignicons-webfont.ttf`

---

## 📝 Resumo Final

| Item | Status |
|------|--------|
| **Pacote instalado?** | ✅ Sim - Material Design Icons v3.7.95 |
| **Localização** | `public/assets/vendors/mdi/` |
| **Ícones vêm do Cerberus?** | ✅ Sim - campo `icon` |
| **Formato necessário** | `mdi mdi-nome-do-icone` |
| **CSS carregado?** | ✅ Sim - em `app.blade.php` |

---

**Última atualização:** 2026-02-12  
**Versão MDI:** 3.7.95

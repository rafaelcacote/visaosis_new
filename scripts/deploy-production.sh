#!/usr/bin/env bash
set -euo pipefail

# Deploy Produção — branch main
# Corre no servidor Hostinger VPS via SSH (manual).
# NÃO corre migrations (protege dados). Corre migrate à mão quando precisares.
#
# Uso (como root):
#   bash /home/visaosis-prod/htdocs/www.visaosis.com.br/scripts/deploy-production.sh
#
# ou:
#   cd /home/visaosis-prod/htdocs/www.visaosis.com.br
#   bash scripts/deploy-production.sh

APP_DIR="/home/visaosis-prod/htdocs/www.visaosis.com.br"
WEB_USER="visaosis-prod"
BRANCH="main"
SITE_URL="https://www.visaosis.com.br"

if [[ ! -d "${APP_DIR}/.git" ]]; then
  echo "ERRO: ${APP_DIR} não existe ou não é um repositório git."
  echo "Faz primeiro o clone (uma vez):"
  echo "  git clone -b main git@github.com:rafaelcacote/visaosis_new.git ${APP_DIR}"
  echo "  # (ou HTTPS: https://github.com/rafaelcacote/visaosis_new.git)"
  exit 1
fi

cd "${APP_DIR}"

echo "==> Manutenção ON"
php artisan down --retry=60 || true

echo "==> Atualizar código (${BRANCH})"
git fetch origin
git checkout "${BRANCH}"
git reset --hard "origin/${BRANCH}"

echo "==> Composer"
# --no-scripts: evita falha do Composer em alguns ambientes Hostinger (proc_open).
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
php artisan package:discover --ansi

# Migrations NÃO correm no deploy. Quando precisares de schema novo:
#   cd ${APP_DIR}
#   php artisan migrate --force

echo "==> Assets (Vite)"
if command -v npm >/dev/null 2>&1; then
  npm ci --no-audit --no-fund
  npm run build
else
  echo "AVISO: npm não encontrado — pula build. Envia public/build manualmente se necessário."
fi

echo "==> Storage link"
php artisan storage:link 2>/dev/null || true

echo "==> Caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Permissões"
chown -R "${WEB_USER}:${WEB_USER}" "${APP_DIR}" 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "==> Manutenção OFF"
php artisan up

echo "==> Deploy produção concluído."
echo "    Site: ${SITE_URL}"
echo "    Se houve migration nova, corre manualmente: php artisan migrate --force"

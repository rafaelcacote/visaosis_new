#!/usr/bin/env bash
# Gera senhas e os .env do Visaosis. Rode no servidor, em /opt/stack.
# Não recria o .env do Postgres nem os env do Cerberus.
set -euo pipefail

cd /opt/stack
umask 077
mkdir -p visaosis-env

if [[ ! -f .env ]]; then
  echo "Falta /opt/stack/.env do stack compartilhado." >&2
  exit 1
fi

secrets="visaosis-env/secrets"
if [[ ! -f "${secrets}" ]]; then
  cat > "${secrets}" <<EOF
VISAOSIS_PROD_DB_PASSWORD=$(openssl rand -hex 24)
VISAOSIS_DEV_DB_PASSWORD=$(openssl rand -hex 24)
VISAOSIS_PROD_APP_KEY=base64:$(openssl rand -base64 32)
VISAOSIS_DEV_APP_KEY=base64:$(openssl rand -base64 32)
EOF
fi

set -a
# shellcheck disable=SC1091
source .env
# shellcheck disable=SC1091
source "${secrets}"
set +a

write_app_env() {
  local target="$1"
  local app_env="$2"
  local app_debug="$3"
  local app_url="$4"
  local app_key="$5"
  local db_database="$6"
  local db_username="$7"
  local db_password="$8"
  local log_level="$9"
  local cerberus_url="${10}"
  local cerberus_db="${11}"
  local cerberus_user="${12}"
  local cerberus_password="${13}"

  cat > "${target}" <<EOF
APP_NAME=Visaosis
APP_ENV=${app_env}
APP_KEY=${app_key}
APP_DEBUG=${app_debug}
APP_URL=${app_url}
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=${log_level}
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=${db_database}
DB_USERNAME=${db_username}
DB_PASSWORD=${db_password}
DB_SCHEMA=visaosis,geral,atendimento,comercial,public
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@visaosis.com.br"
MAIL_FROM_NAME="Visaosis"
CERBERUS_URL=${cerberus_url}
CERBERUS_SYSTEM_KEY=visaosis
CERBERUS_DB_HOST=postgres
CERBERUS_DB_PORT=5432
CERBERUS_DB_DATABASE=${cerberus_db}
CERBERUS_DB_USERNAME=${cerberus_user}
CERBERUS_DB_PASSWORD=${cerberus_password}
EOF
}

write_app_env visaosis-env/prod.env production false https://app.visaosis.com.br \
  "${VISAOSIS_PROD_APP_KEY}" visaosis_prod visaosis_prod "${VISAOSIS_PROD_DB_PASSWORD}" error \
  https://cerberus.otimisoft.com.br cerberus_prod cerberus_prod "${CERBERUS_PROD_DB_PASSWORD}"

write_app_env visaosis-env/dev.env local true https://dev.visaosis.com.br \
  "${VISAOSIS_DEV_APP_KEY}" visaosis_dev visaosis_dev "${VISAOSIS_DEV_DB_PASSWORD}" debug \
  https://cerberusdev.otimisoft.com.br cerberus_dev cerberus_dev "${CERBERUS_DEV_DB_PASSWORD}"

echo "visaosis env files ready"

#!/usr/bin/env bash
# Cria visaosis_prod e visaosis_dev e restaura o dump custom do PostgreSQL 18.
# Não mexe nos databases do Cerberus.
set -euo pipefail

cd /opt/stack
set -a
# shellcheck disable=SC1091
source .env
# shellcheck disable=SC1091
source env/visaosis.secrets
set +a

DUMP="${1:-/opt/stack/visaosis_20261022_213710.dump}"
if [[ ! -f "${DUMP}" ]]; then
  echo "Dump não encontrado: ${DUMP}" >&2
  exit 1
fi

docker compose exec -T postgres psql -v ON_ERROR_STOP=1 -U postgres <<SQL
DO \$\$
BEGIN
  IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'visaosis_prod') THEN
    CREATE ROLE visaosis_prod LOGIN PASSWORD '${VISAOSIS_PROD_DB_PASSWORD}';
  ELSE
    ALTER ROLE visaosis_prod WITH LOGIN PASSWORD '${VISAOSIS_PROD_DB_PASSWORD}';
  END IF;
  IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'visaosis_dev') THEN
    CREATE ROLE visaosis_dev LOGIN PASSWORD '${VISAOSIS_DEV_DB_PASSWORD}';
  ELSE
    ALTER ROLE visaosis_dev WITH LOGIN PASSWORD '${VISAOSIS_DEV_DB_PASSWORD}';
  END IF;
END
\$\$;
SELECT 'CREATE DATABASE visaosis_prod OWNER visaosis_prod'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'visaosis_prod')\gexec
SELECT 'CREATE DATABASE visaosis_dev OWNER visaosis_dev'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'visaosis_dev')\gexec
SQL

restore_one() {
  local db="$1"
  local code
  echo "→ restore ${db}"
  docker compose cp "${DUMP}" "postgres:/tmp/visaosis.dump"
  set +e
  docker compose exec -T postgres pg_restore -U postgres --no-owner --no-acl --role="${db}" --dbname="${db}" /tmp/visaosis.dump
  code=$?
  set -e
  docker compose exec -T postgres psql -v ON_ERROR_STOP=1 -U postgres -d "${db}" <<SQL
ALTER ROLE ${db} SET search_path TO visaosis, geral, atendimento, comercial, public;
GRANT USAGE ON SCHEMA visaosis, geral, atendimento, comercial TO ${db};
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA visaosis, geral, atendimento, comercial TO ${db};
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA visaosis, geral, atendimento, comercial TO ${db};
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA visaosis, geral, atendimento, comercial TO ${db};
ALTER SCHEMA visaosis OWNER TO ${db};
ALTER SCHEMA geral OWNER TO ${db};
ALTER SCHEMA atendimento OWNER TO ${db};
ALTER SCHEMA comercial OWNER TO ${db};
SQL
  RESTORE_CODE="${code}"
}

restore_one visaosis_prod
prod_code="${RESTORE_CODE}"
restore_one visaosis_dev
dev_code="${RESTORE_CODE}"

docker compose exec -T postgres rm -f /tmp/visaosis.dump

echo "→ conferência"
docker compose exec -T postgres psql -U postgres -d visaosis_prod -c \
  "SELECT 'prod' AS env, (SELECT count(*) FROM geral.pessoa) AS pessoas, (SELECT count(*) FROM visaosis.users) AS users, (SELECT count(*) FROM visaosis.migrations) AS migrations;"
docker compose exec -T postgres psql -U postgres -d visaosis_dev -c \
  "SELECT 'dev' AS env, (SELECT count(*) FROM geral.pessoa) AS pessoas, (SELECT count(*) FROM visaosis.users) AS users, (SELECT count(*) FROM visaosis.migrations) AS migrations;"

if [[ "${prod_code}" -gt 1 || "${dev_code}" -gt 1 ]]; then
  echo "pg_restore falhou (prod=${prod_code}, dev=${dev_code})" >&2
  exit 1
fi

echo "restore concluído (prod=${prod_code}, dev=${dev_code})"

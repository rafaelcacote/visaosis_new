-- Tipo da categoria (produto/serviço) na categoria de produto (executar manualmente no PostgreSQL)
-- Schema: comercial

BEGIN;

ALTER TABLE comercial.categoria_produto
    ADD COLUMN IF NOT EXISTS tipo VARCHAR(20) DEFAULT 'produto' NOT NULL;

COMMENT ON COLUMN comercial.categoria_produto.tipo IS 'Tipo da categoria: produto ou servico (padrão: produto)';

ALTER TABLE comercial.categoria_produto
    DROP CONSTRAINT IF EXISTS categoria_produto_tipo_check;

ALTER TABLE comercial.categoria_produto
    ADD CONSTRAINT categoria_produto_tipo_check CHECK (tipo IN ('produto', 'servico'));

COMMIT;

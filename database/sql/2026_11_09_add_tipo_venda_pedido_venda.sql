-- Tipo de venda (atacado/varejo) no pedido de venda (executar manualmente no PostgreSQL)
-- Schema: comercial

BEGIN;

ALTER TABLE comercial.pedido_venda
    ADD COLUMN IF NOT EXISTS tipo_venda VARCHAR(20) DEFAULT 'varejo' NOT NULL;

COMMENT ON COLUMN comercial.pedido_venda.tipo_venda IS 'Tipo da venda: atacado ou varejo (padrão: varejo)';

ALTER TABLE comercial.pedido_venda
    DROP CONSTRAINT IF EXISTS pedido_venda_tipo_venda_check;

ALTER TABLE comercial.pedido_venda
    ADD CONSTRAINT pedido_venda_tipo_venda_check CHECK (tipo_venda IN ('atacado', 'varejo'));

COMMIT;

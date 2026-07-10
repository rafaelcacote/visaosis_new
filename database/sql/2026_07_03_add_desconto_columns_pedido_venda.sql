-- Desconto no pedido de venda (executar manualmente no PostgreSQL)
-- Schema: comercial

BEGIN;

ALTER TABLE comercial.pedido_venda
    ADD COLUMN IF NOT EXISTS desconto_valor NUMERIC(12, 2) DEFAULT 0 NOT NULL;

ALTER TABLE comercial.pedido_venda
    ADD COLUMN IF NOT EXISTS desconto_percentual NUMERIC(5, 2) DEFAULT 0 NOT NULL;

ALTER TABLE comercial.pedido_venda
    ADD COLUMN IF NOT EXISTS desconto_autorizado_por BIGINT NULL;

COMMENT ON COLUMN comercial.pedido_venda.desconto_valor IS 'Valor absoluto do desconto aplicado no pedido';
COMMENT ON COLUMN comercial.pedido_venda.desconto_percentual IS 'Percentual de desconto aplicado no pedido';
COMMENT ON COLUMN comercial.pedido_venda.desconto_autorizado_por IS 'ID do usuário (Cerberus) que autorizou o desconto';

-- Preencher desconto retroativo nas vendas já existentes (subtotal dos itens - valor_total)
UPDATE comercial.pedido_venda pv
SET desconto_valor = subtotais.subtotal - pv.valor_total
FROM (
    SELECT
        ip.pedido_id,
        COALESCE(SUM(ip.preco_unit * ip.quantidade), 0) AS subtotal
    FROM comercial.item_pedido ip
    WHERE ip.deleted_at IS NULL
    GROUP BY ip.pedido_id
) AS subtotais
WHERE pv.id = subtotais.pedido_id
  AND pv.desconto_valor = 0
  AND subtotais.subtotal > pv.valor_total;

COMMIT;

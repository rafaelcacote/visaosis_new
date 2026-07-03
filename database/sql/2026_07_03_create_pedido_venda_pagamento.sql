-- Cria a tabela de formas de pagamento por pedido de venda
-- Permite múltiplas formas de pagamento por venda (ex: Dinheiro + PIX)

CREATE TABLE IF NOT EXISTS comercial.pedido_venda_pagamento (
    id               BIGSERIAL PRIMARY KEY,
    tenant_id        BIGINT NOT NULL,
    location_id      BIGINT,
    pedido_venda_id  BIGINT NOT NULL,
    forma_pagamento  VARCHAR(100) NOT NULL,
    valor            NUMERIC(12, 2) NOT NULL,
    parcelas         INT NOT NULL DEFAULT 1,
    created_at       TIMESTAMP WITH TIME ZONE,
    updated_at       TIMESTAMP WITH TIME ZONE,
    deleted_at       TIMESTAMP WITH TIME ZONE
);

CREATE INDEX IF NOT EXISTS idx_pvp_pedido_venda_id
    ON comercial.pedido_venda_pagamento (pedido_venda_id);

CREATE INDEX IF NOT EXISTS idx_pvp_tenant_id
    ON comercial.pedido_venda_pagamento (tenant_id);

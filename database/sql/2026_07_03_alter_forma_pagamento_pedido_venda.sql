-- Aumenta o limite da coluna forma_pagamento em pedido_venda
-- para suportar múltiplas formas concatenadas (ex: "Cartão de Crédito + Crediário")

ALTER TABLE comercial.pedido_venda
    ALTER COLUMN forma_pagamento TYPE VARCHAR(200);

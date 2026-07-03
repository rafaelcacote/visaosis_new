<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Permite cadastrar produto sem preço de venda definido.
     * Tabela física: comercial.produto
     */
    public function up(): void
    {
        $exists = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'comercial'
              AND table_name = 'produto'
              AND column_name = 'preco_venda'
        ");

        if (!$exists) {
            return;
        }

        DB::statement('ALTER TABLE comercial.produto ALTER COLUMN preco_venda DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $exists = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = 'comercial'
              AND table_name = 'produto'
              AND column_name = 'preco_venda'
        ");

        if (!$exists) {
            return;
        }

        DB::statement('UPDATE comercial.produto SET preco_venda = 0 WHERE preco_venda IS NULL');
        DB::statement('ALTER TABLE comercial.produto ALTER COLUMN preco_venda SET NOT NULL');
    }
};

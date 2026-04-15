<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conta_receber', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('pedido_venda_id');
            $table->unsignedBigInteger('pessoa_cliente_id');
            $table->unsignedInteger('numero_parcela');
            $table->unsignedInteger('total_parcelas');
            $table->decimal('valor_parcela', 12, 2);
            $table->decimal('valor_total_venda', 12, 2);
            $table->date('data_vencimento');
            $table->timestamp('data_pagamento')->nullable();
            $table->string('forma_pagamento', 50);
            $table->string('status', 30)->default('pendente');
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['pedido_venda_id', 'numero_parcela']);
            $table->index('pessoa_cliente_id');
            $table->index('data_vencimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conta_receber');
    }
};

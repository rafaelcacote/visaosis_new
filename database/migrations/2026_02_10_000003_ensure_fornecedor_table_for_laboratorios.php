<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona à tabela fornecedor (já existente) as colunas usadas para laboratórios, se faltarem.
     */
    public function up(): void
    {
        if (!Schema::hasTable('fornecedor')) {
            return;
        }

        Schema::table('fornecedor', function (Blueprint $table) {
            if (!Schema::hasColumn('fornecedor', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'nome')) {
                $table->string('nome')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'endereco')) {
                $table->string('endereco')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'telefone')) {
                $table->string('telefone', 30)->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'prazo_entrega')) {
                $table->unsignedSmallInteger('prazo_entrega')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'especialidades')) {
                $table->text('especialidades')->nullable();
            }
            if (!Schema::hasColumn('fornecedor', 'ativo')) {
                $table->boolean('ativo')->default(true);
            }
            if (!Schema::hasColumn('fornecedor', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não removemos nada de fornecedor automaticamente
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona à tabela fornecedores (já existente) as colunas usadas para laboratórios, se faltarem.
     */
    public function up(): void
    {
        if (!Schema::hasTable('fornecedores')) {
            return;
        }

        Schema::table('fornecedores', function (Blueprint $table) {
            if (!Schema::hasColumn('fornecedores', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'nome')) {
                $table->string('nome')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'endereco')) {
                $table->string('endereco')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'telefone')) {
                $table->string('telefone', 30)->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'prazo_entrega')) {
                $table->unsignedSmallInteger('prazo_entrega')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'especialidades')) {
                $table->text('especialidades')->nullable();
            }
            if (!Schema::hasColumn('fornecedores', 'ativo')) {
                $table->boolean('ativo')->default(true);
            }
            if (!Schema::hasColumn('fornecedores', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não removemos a tabela fornecedores pois já existia; apenas não desfazemos colunas adicionadas
    }
};

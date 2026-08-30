<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescricao', function (Blueprint $table) {
            $table->string('receita_foto_caminho', 255)->nullable()
                ->after('observacoes')
                ->comment('Caminho relativo no storage (disk=public) da foto/anexo da receita.');
        });
    }

    public function down(): void
    {
        Schema::table('prescricao', function (Blueprint $table) {
            $table->dropColumn('receita_foto_caminho');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profissional', function (Blueprint $table) {
            if (! Schema::hasColumn('profissional', 'assinatura_caminho')) {
                $table->string('assinatura_caminho')->nullable()->after('chave_pix');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profissional', function (Blueprint $table) {
            if (Schema::hasColumn('profissional', 'assinatura_caminho')) {
                $table->dropColumn('assinatura_caminho');
            }
        });
    }
};

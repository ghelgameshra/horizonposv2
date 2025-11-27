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
        Schema::table('tutup_harian_detail', function (Blueprint $table) {
            $table->integer('pengeluaran_csh')->default(0)->after('piutang');
            $table->integer('pengeluaran_trf')->default(0)->after('pengeluaran_csh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutup_harian_detail', function (Blueprint $table) {
            $table->dropColumn('pengeluaran_csh');
            $table->dropColumn('pengeluaran_trf');
        });
    }
};

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
        Schema::table('transaksi_log', function (Blueprint $table) {
            $table->string('kode_promo', 10)->nullable()->after('nama_produk');
            $table->integer('potongan')->default(0)->after('ukuran');
            $table->integer('gross')->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_log', function (Blueprint $table) {
            $table->dropColumn('kode_promo');
            $table->dropColumn('potongan');
            $table->dropColumn('gross');
        });
    }
};

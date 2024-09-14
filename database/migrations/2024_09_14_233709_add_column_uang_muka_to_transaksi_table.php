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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->integer('uang_muka')->after('bayar')->default(0);
        });

        Schema::table('transaksi_log', function (Blueprint $table) {
            $table->integer('jumlah')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('uang_muka');
        });

        Schema::table('transaksi_log', function (Blueprint $table) {
            $table->tinyInteger('jumlah')->default(0);
        });
    }
};

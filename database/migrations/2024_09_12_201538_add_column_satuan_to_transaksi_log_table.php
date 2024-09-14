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
            $table->string('satuan', 25)->after('jumlah')->nullable();
            $table->tinyInteger('jumlah')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_log', function (Blueprint $table) {
            $table->dropColumn('satuan');
            $table->tinyInteger('jumlah')->default(1)->change();
        });
    }
};

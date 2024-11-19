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
            $table->string('worker', 200)->after('informasi_stok')->nullable();
            $table->string('worker_addid')->after('worker')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_log', function (Blueprint $table) {
            $table->dropColumn('worker');
            $table->dropColumn('worker_addid');
        });
    }
};

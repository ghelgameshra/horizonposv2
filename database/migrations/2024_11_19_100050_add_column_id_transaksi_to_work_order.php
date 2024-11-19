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
        Schema::table('work_order', function (Blueprint $table) {
            $table->unsignedBigInteger('id_transaksi')->after('id');
            $table->foreign('id_transaksi')->references('id')->on('transaksi')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->dropForeign('work_order_id_transaksi_foreign');
            $table->dropColumn('id_transaksi');
        });
    }
};

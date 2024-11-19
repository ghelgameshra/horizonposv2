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
        Schema::create('work_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_transaksi_log');
            $table->string('namafile', 200)->nullable();
            $table->string('nama_produk', 200)->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('ukuran', 100)->nullable();
            $table->string('kategori', 200)->nullable();
            $table->string('worker', 200)->nullable();
            $table->string('addid', 100)->nullable();
            $table->string('progress', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_transaksi_log')->references('id')->on('transaksi_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order');
    }
};

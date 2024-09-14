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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('invno', 20)->nullable()->unique();
            $table->unsignedBigInteger('id_member')->nullable();
            $table->string('nama_customer', 100)->nullable();
            $table->string('kode_promo', 8)->nullable();
            $table->string('nomor_telepone', 14)->nullable();
            $table->date('tanggal_transaksi');
            $table->integer('subtotal')->default(0);
            $table->integer('diskon')->default(0);
            $table->integer('total')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('terima')->default(0);
            $table->integer('kembali')->default(0);
            $table->string('tipe_bayar', 5)->nullable();
            $table->unsignedBigInteger('kasir_id');
            $table->string('status_order', 25)->nullable();
            $table->string('addid', 20)->nullable();
            $table->timestamps();

            $table->foreign('id_member')->references('id')->on('member');
            $table->foreign('kasir_id')->references('id')->on('users');
        });

        Schema::create('transaksi_log', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi');
            $table->string('namafile', 100)->nullable();
            $table->string('plu', 10);
            $table->string('nama_produk', 200)->nullable();
            $table->unsignedBigInteger('id_kategori');
            $table->integer('harga_jual')->default(0);
            $table->tinyInteger('jumlah')->default(1);
            $table->string('ukuran', 25)->nullable();
            $table->integer('total')->default(0);
            $table->string('informasi_stok', 200)->nullable();
            $table->string('status_order', 25)->nullable();
            $table->timestamps();

            $table->foreign('id_kategori')->references('id')->on('ref_kategori');
        });

        Schema::create('ref_status_order', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 25);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('transaksi_log');
        Schema::dropIfExists('ref_status_order');
    }
};

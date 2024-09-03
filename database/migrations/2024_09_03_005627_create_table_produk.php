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
        Schema::create('ref_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 150);
            $table->text('deskripsi')->nullable();
            $table->string('addid')->nullable();
            $table->timestamps();
        });

        Schema::create('ref_jenis_ukuran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 150);
            $table->text('deskripsi')->nullable();
            $table->string('addid')->nullable();
            $table->timestamps();
        });

        Schema::create('ref_satuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan', 150);
            $table->text('deskripsi')->nullable();
            $table->string('addid')->nullable();
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('plu', 10)->unique()->nullable();
            $table->string('nama_produk');
            $table->unsignedBigInteger('id_kategori');
            $table->integer('harga_beli')->default(0);
            $table->integer('harga_jual')->default(0);
            $table->string('merk')->nullable();
            $table->integer('stok')->default(0);
            $table->integer('addno')->default(0);
            $table->string('jenis_ukuran', 25)->nullable();
            $table->string('satuan', 25)->nullable();
            $table->boolean('plu_aktif')->default(true);
            $table->boolean('jual_minus')->default(true);
            $table->boolean('retur')->default(false);
            $table->string('kode_suppllier', 4)->nullable();
            $table->string('addid')->nullable();
            $table->timestamps();

            $table->foreign('id_kategori')->references('id')->on('ref_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
        Schema::dropIfExists('ref_kategori');
        Schema::dropIfExists('ref_jenis_ukuran');
        Schema::dropIfExists('ref_satuan');
    }
};

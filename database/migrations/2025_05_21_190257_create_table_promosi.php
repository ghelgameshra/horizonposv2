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
        Schema::create('promosi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_promo', 10)->unique();
            $table->string('nama_promo', 100)->unique();
            $table->text('detail_promo',200)->nullable();
            $table->boolean('promo_member')->default(false);

            /* nilai */
            $table->enum('tipe_promo', ["MEMBER", "PRODUK", "TOTAL"])->default("PRODUK");
            $table->enum('tipe_potongan', ['$', '%'])->default('$');
            $table->decimal('nilai_potongan')->default(0);
            $table->integer('nominal_min_pembelian')->default(0);
            $table->integer('nominal_maks_pembelian')->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->bigInteger('total_penggunaan')->default(0);
            $table->boolean('status_promo')->default(false);
            $table->string('addid', 50)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promosi');
    }
};

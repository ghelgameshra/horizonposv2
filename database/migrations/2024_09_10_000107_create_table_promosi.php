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
        Schema::create('promo', function (Blueprint $table) {
            $table->id();
            $table->string('kode_promo', 8)->unique();
            $table->text('item_syarat');
            $table->boolean('promo_member')->default(true);
            $table->integer('minimal_belanja')->default(0);
            $table->integer('maksimal_belanja')->default(0);
            $table->tinyInteger('potongan_persentase')->default(0);
            $table->integer('potongan_langsung')->default(0);
            $table->integer('potongan_minimal')->default(0);
            $table->integer('potongan_maksimal')->default(0);
            $table->string('jenis_promo');
            $table->text('mekanisme_promo');
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->bigInteger('total_penggunaan')->default(0);
            $table->string('addid', 25)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo');
    }
};

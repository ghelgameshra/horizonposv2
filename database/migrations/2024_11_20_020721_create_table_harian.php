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
        Schema::create('tutup_harian', function (Blueprint $table) {
            $table->id();
            $table->string('invno', 50)->unique()->nullable();
            $table->date('tanggal_harian')->nullable();
            $table->integer('rp100000')->default(0);
            $table->integer('rp75000')->default(0);
            $table->integer('rp50000')->default(0);
            $table->integer('rp20000')->default(0);
            $table->integer('rp10000')->default(0);
            $table->integer('rp5000')->default(0);
            $table->integer('rp1000')->default(0);
            $table->integer('rp2000')->default(0);
            $table->integer('rp500')->default(0);
            $table->integer('rp200')->default(0);
            $table->integer('rp100')->default(0);
            $table->integer('rptotal')->default(0);
            $table->string('user', 100);
            $table->timestamps();
        });

        Schema::create('tutup_harian_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_harian');
            $table->integer('pesanan_selesai')->default(0);
            $table->integer('pesanan_cancel')->default(0);
            $table->integer('jumlah_sales')->default(0);
            $table->integer('total_nominal_sales')->default(0);
            $table->integer('jumlah_sales_dikson')->default(0);
            $table->integer('total_nominal_diskon')->default(0);

            $table->integer('jumlah_bayar_dpcsh')->default(0);
            $table->integer('total_bayar_dpcsh')->default(0);
            $table->integer('jumlah_bayar_csh')->default(0);
            $table->integer('total_bayar_csh')->default(0);

            $table->integer('jumlah_bayar_dptrf')->default(0);
            $table->integer('total_bayar_dptrf')->default(0);
            $table->integer('jumlah_bayar_trf')->default(0);
            $table->integer('total_bayar_trf')->default(0);

            $table->integer('piutang')->default(0);
            $table->integer('rptotal')->default(0);
            $table->integer('selisih_fisik')->default(0);
            $table->timestamps();

            $table->foreign('id_harian')->references('id')->on('tutup_harian')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutup_harian_detail');
        Schema::dropIfExists('tutup_harian');
    }
};

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
        Schema::create('ref_jenis_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengeluaran', 100)->unique();
            $table->timestamps();
        });

        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('invno', 20)->nullable();
            $table->date('tanggal_pengeluaran')->nullable();
            $table->string('jenis_pengeluaran', 150);
            $table->integer('total');
            $table->string('tipe_bayar', 5)->default('CSH');
            $table->unsignedBigInteger('user_id');
            $table->string('image', 200)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jenis_pengeluaran');
        Schema::dropIfExists('pengeluaran');
    }
};

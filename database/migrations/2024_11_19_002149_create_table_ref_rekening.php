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
        Schema::create('ref_rekening', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 50);
            $table->string('nomor_rekening', 100);
            $table->string('nama_pemilik', 150);
            $table->string('addid', 100)->nullable();
            $table->string('updid', 100)->nullable();
            $table->boolean('default')->default(false);
            $table->timestamps();

            $table->unique(['nama_bank', 'nomor_rekening', 'nama_pemilik']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_rekening');
    }
};

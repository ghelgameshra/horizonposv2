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
        Schema::create('toko', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan', 200);
            $table->string('email', 50);
            $table->string('telepone', 15);
            $table->string('whatsapp', 15);
            $table->string('instagram', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('tiktok', 100)->nullable();
            $table->string('web', 200)->default('ghelgameshra.github.io')->nullable();
            $table->string('alamat_lengkap', 250);
            $table->string('logo')->default('default/images/logo_default.png');
            $table->string('qr_wa')->default('default/images/qr-wa.jpeg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko');
    }
};

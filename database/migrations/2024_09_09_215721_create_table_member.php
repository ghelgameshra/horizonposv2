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
        Schema::create('const', function (Blueprint $table) {
            $table->id();
            $table->string('key', 4)->unique();
            $table->bigInteger('docno')->default(0);
            $table->boolean('aktif')->default(true);
            $table->string('keterangan', 150);
            $table->timestamps();
        });

        Schema::create('member', function (Blueprint $table) {
            $table->id();
            $table->string('kode_member')->nullable()->unique();
            $table->string('nama_lengkap', 150)->unique();
            $table->text('alamat_lengkap');
            $table->string('telepone', 14)->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member');
        Schema::dropIfExists('const');
    }
};

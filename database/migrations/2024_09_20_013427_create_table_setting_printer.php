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
        Schema::create('setting_printer', function (Blueprint $table) {
            $table->id();
            $table->string('nama_printer', 50)->unique();
            $table->string('jenis_printer', 25)->unique();
            $table->string('protocol_printer', 25)->default('SMB');
            $table->string('ip_printer', 25)->nullable();
            $table->string('port_printer', 15)->nullable();
            $table->string('username_printer')->nullable();
            $table->string('password_printer')->nullable();
            $table->boolean('default_printer')->default(true);
            $table->string('keterangan_printer', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_printer');
    }
};

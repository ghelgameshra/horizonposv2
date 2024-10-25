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
        Schema::create('users_api', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150)->unique()->nullable();
            $table->string('nik', 150)->unique()->nullable();
            $table->text('password');
            $table->string('auth_token', 150)->nullable();
            $table->string('salt', 32)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_api');
    }
};

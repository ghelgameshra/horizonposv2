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
        Schema::create('setting_struk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_setting', 50);
            $table->string('key', 4)->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('setting_pesan_struk', function (Blueprint $table) {
            $table->id();
            $table->string('pesan', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_struk');
        Schema::dropIfExists('setting_pesan_struk');
    }
};

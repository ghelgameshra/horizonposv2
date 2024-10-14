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
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->string('key', 4)->unique();
            $table->tinyInteger('docno')->default(0);
            $table->tinyInteger('meja')->default(0);
            $table->date('periode');
            $table->string('keterangan', 150)->nullable();
            $table->string('pengucapan', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};

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
        Schema::create('promosi_plu_larangan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_promosi');
            $table->string('kode_promo', 10);
            $table->string('plu', 10);
            $table->string('addid', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_promosi')->references('id')->on('promosi')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promosi_plu_larangan');
    }
};

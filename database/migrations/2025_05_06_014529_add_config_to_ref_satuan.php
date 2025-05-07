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
        Schema::table('ref_satuan', function (Blueprint $table) {
            $table->boolean('input_namafile')->after('nama_satuan')->default(true);
            $table->boolean('input_ukuran')->after('input_namafile')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_satuan', function (Blueprint $table) {
            $table->dropColumn('input_namafile');
            $table->dropColumn('input_ukuran');
        });
    }
};

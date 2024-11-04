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
        Schema::table('produk', function (Blueprint $table) {
            $table->string('addid', 100)->default('127.0.0.1')->change();
            $table->string('updid', 100)->default('127.0.0.1')->after('addid');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('addid', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->string('addid')->change();
            $table->dropColumn('updid');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('addid', 20)->default('127.0.0.1')->nullable()->change();
        });
    }
};

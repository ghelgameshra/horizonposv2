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
        Schema::create('ref_pendidikan_terakhir', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenjang', 150);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('ref_agama', function (Blueprint $table) {
            $table->id();
            $table->string('nama_agama', 150);
            $table->timestamps();
        });

        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 150);
            $table->string('nik', 18)->unique();
            $table->string('jabatan', 50);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->text('alamat_domisili');
            $table->string('telepone', 14)->unique();
            $table->string('jobdesk', 50)->nullable();
            $table->string('agama', 50);
            $table->string('ktp', 18)->unique();
            $table->boolean('status_pernikahan')->default(false);
            $table->string('pendidikan_terakhir', 100);
            $table->date('tanggal_masuk')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
        Schema::dropIfExists('ref_pendidikan_terakhir');
        Schema::dropIfExists('ref_agama');
    }
};

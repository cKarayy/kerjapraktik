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
        Schema::create('laporan_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai');
            $table->string('kehadiran'); // Hadir, Izin, Cuti
            $table->string('shift'); // Shift Pagi, Middle, Malam
            $table->string('lateness')->nullable(); // Keterlambatan
            $table->timestamp('tanggal_scan')->useCurrent(); // Waktu Scan QR
            $table->string('bulan'); // Bulan untuk akumulasi keterlambatan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kehadiran');
    }
};

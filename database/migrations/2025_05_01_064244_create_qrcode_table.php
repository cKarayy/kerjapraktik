<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('qr_code', function (Blueprint $table) {
            $table->id('id_code');
            $table->unsignedBigInteger('id_admin');
            $table->string('kode');
            $table->dateTime('waktu_generate');
            $table->string('kehadiran')->default('belum hadir');;
            $table->timestamps();

            $table->foreign('id_admin')->references('id_admin')->on('admins')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qrcode');
    }
};

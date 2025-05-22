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
        Schema::table('qr_code', function (Blueprint $table) {
            $table->unsignedBigInteger('id_shift')->nullable();
            $table->foreign('id_shift')->references('id_shift')->on('shifts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_code', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['id_shift']);
            // Baru drop kolomnya
            $table->dropColumn('id_shift');
        });
    }
};

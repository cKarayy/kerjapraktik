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
        Schema::table('absensis', function (Blueprint $table) {
            // Menambahkan kolom baru
            $table->unsignedBigInteger('id_shift')->after('id_admin'); // Menambah kolom ID shift
            $table->integer('keterlambatan')->nullable()->after('kehadiran'); // Kolom keterlambatan

            // Menambahkan foreign key untuk id_shift
            $table->foreign('id_shift')->references('id_shift')->on('shifts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Menghapus kolom yang baru ditambahkan
            $table->dropForeign(['id_shift']);
            $table->dropColumn('id_shift');
            $table->dropColumn('keterlambatan');
        });
    }
};

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
        Schema::table('izins', function (Blueprint $table) {
            $table->unsignedBigInteger('id_shift')->nullable()->after('id_karyawan');
            $table->foreign('id_shift')->references('id_shift')->on('shifts')->onDelete('set null');
        });

        Schema::table('cutis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_shift')->nullable()->after('id_karyawan');
            $table->foreign('id_shift')->references('id_shift')->on('shifts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            $table->dropForeign(['id_shift']);
            $table->dropColumn('id_shift');
        });

        Schema::table('cutis', function (Blueprint $table) {
            $table->dropForeign(['id_shift']);
            $table->dropColumn('id_shift');
        });
    }
};

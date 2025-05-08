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
        Schema::table('absensis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_code')->nullable()->after('id_shift');

            // Tambahkan foreign key ke qr_code
            $table->foreign('id_code')->references('id_code')->on('qr_code')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['id_code']);
            $table->dropColumn('id_code');
        });
    }
};

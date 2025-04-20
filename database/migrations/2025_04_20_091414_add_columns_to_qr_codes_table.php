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
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id'); // Tambahkan UUID setelah ID
            $table->string('shift')->after('code'); // Tambahkan kolom shift setelah kolom code
            $table->timestamp('scanned_at')->nullable()->after('is_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            //
        });
    }
};

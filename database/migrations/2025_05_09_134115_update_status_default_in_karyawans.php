<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStatusDefaultInKaryawans extends Migration
{
    public function up()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Mengubah kolom status dengan default 'active'
            $table->string('status')->default('active')->change();
        });
    }

    public function down()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Membalik perubahan jika rollback
            $table->string('status')->default('inactive')->change();
        });
    }
}

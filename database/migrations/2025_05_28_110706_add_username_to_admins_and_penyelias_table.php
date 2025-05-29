<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameToAdminsAndPenyeliasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Menambahkan kolom 'username' ke tabel 'admins'
        Schema::table('admins', function (Blueprint $table) {
            $table->string('username')->unique()->after('nama_lengkap');
        });

        // Menambahkan kolom 'username' ke tabel 'penyelia'
        Schema::table('penyelias', function (Blueprint $table) {
            $table->string('username')->unique()->after('nama_lengkap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus kolom 'username' dari tabel 'admins'
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('username');
        });

        // Menghapus kolom 'username' dari tabel 'penyelia'
        Schema::table('penyelias', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}


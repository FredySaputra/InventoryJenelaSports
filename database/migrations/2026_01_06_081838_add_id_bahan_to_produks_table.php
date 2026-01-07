<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->string('idBahan', 100)->nullable()->after('idKategori');

            $table->foreign('idBahan')->references('id')->on('bahans');
        });
    }

    public function down()
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('idBahan');
        });
    }
};

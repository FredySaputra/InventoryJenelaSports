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
        Schema::create('produks', function (Blueprint $table) {
            $table->string('id',100)->primary();
            $table->string('nama',100)->nullable(false);
            $table->string('warna',100)->nullable();
            $table->string('idKategori')->nullable(false);
            $table->string('idUser')->nullable(false);

            $table->foreign('idKategori')->on('kategoris')->references('id');
            $table->foreign('idUser')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};

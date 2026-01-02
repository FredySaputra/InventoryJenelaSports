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
        Schema::create('bahans', function (Blueprint $table) {
            $table->string('id',100)->primary();
            $table->string('nama',100)->nullable(false);
            $table->text('deskripsi')->nullable();
            $table->string('idKategori',100)->nullable(false);

            $table->foreign('idKategori')->on('kategoris')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahans');
    }
};

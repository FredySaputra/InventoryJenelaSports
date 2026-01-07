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
        Schema::create('sizes', function (Blueprint $table) {
            $table->string('id',100)->primary();
            $table->string('tipe',100)->nullable(false);
            $table->decimal('panjang')->nullable(false)->default(0);
            $table->decimal('lebar')->nullable(false)->default(0);
            $table->string('idKategori')->nullable(false);

            $table->foreign('idKategori')->on('kategoris')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};

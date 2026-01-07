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
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->string('idTransaksi')->nullable(false);
            $table->string('idProduk')->nullable(false);
            $table->string('idSize')->nullable(false);
            $table->decimal('hargaProduk',15,2)->default(0)->nullable(false);
            $table->integer('jumlah')->default(0)->nullable(false);
            $table->timestamps();
            $table->foreign('idTransaksi')->on('transaksis')->references('id');
            $table->foreign('idProduk')->on('produks')->references('id');
            $table->foreign('idSize')->on('sizes')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksis');
    }
};

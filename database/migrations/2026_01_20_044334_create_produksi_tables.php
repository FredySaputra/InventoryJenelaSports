<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL HEADER SPK (Surat Perintah Kerja)
        Schema::create('perintah_produksis', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // Contoh: SPK-001
            $table->date('tanggal_mulai');
            $table->date('tanggal_target')->nullable();

            // Relasi ke Pelanggan (Opsional: Jika ini pesanan khusus pelanggan)
            // Jika untuk stok gudang saja, ini bisa null
            $table->string('idPelanggan', 100)->nullable();

            $table->enum('status', ['Pending', 'Proses', 'Selesai', 'Dibatalkan'])->default('Pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign Key ke Pelanggan (Jika ada)
            $table->foreign('idPelanggan')->references('id')->on('pelanggans')->onDelete('set null');
        });

        // 2. TABEL DETAIL BARANG YANG HARUS DIBUAT
        Schema::create('detail_perintah_produksis', function (Blueprint $table) {
            $table->id();
            $table->string('idPerintahProduksi', 50);
            $table->string('idProduk', 100);
            $table->string('idSize', 100);

            $table->integer('jumlah_target'); // Target misal: 100 pcs
            $table->integer('jumlah_selesai')->default(0); // Yang sudah jadi: 0 pcs

            $table->foreign('idPerintahProduksi')->references('id')->on('perintah_produksis')->onDelete('cascade');
            $table->foreign('idProduk')->references('id')->on('produks');
            $table->foreign('idSize')->references('id')->on('sizes');
        });

        // 3. TABEL PROGRESS (YANG DI-INPUT KARYAWAN DI ANDROID)
        Schema::create('progres_produksis', function (Blueprint $table) {
            $table->id();

            // Link ke detail pekerjaan mana
            $table->unsignedBigInteger('idDetailProduksi');

            // Siapa karyawan yang mengerjakan
            $table->string('idKaryawan', 40);

            $table->integer('jumlah_disetor'); // Inputan Karyawan (Misal: 20)
            $table->integer('jumlah_diterima')->nullable(); // Konfirmasi Admin (Misal: 18 bagus, 2 reject)

            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->timestamp('waktu_setor'); // Kapan dia input di Android
            $table->timestamp('waktu_konfirmasi')->nullable(); // Kapan admin klik OK

            $table->foreign('idDetailProduksi')->references('id')->on('detail_perintah_produksis')->onDelete('cascade');
            $table->foreign('idKaryawan')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_produksis');
        Schema::dropIfExists('detail_perintah_produksis');
        Schema::dropIfExists('perintah_produksis');
    }
};

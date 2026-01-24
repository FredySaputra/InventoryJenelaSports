@extends('layouts.admin')

@section('title', 'Verifikasi Hasil Produksi')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold m-0 text-primary">Verifikasi Setoran Karyawan</h5>
                <small class="text-muted">Validasi hasil kerja sebelum masuk stok.</small>
            </div>
            <button class="btn btn-sm btn-outline-primary" onclick="loadVerifikasi()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Waktu Setor</th>
                        <th>Karyawan</th>
                        <th>Info Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="tableData">
                    <tr><td colspan="5" class="text-center py-5">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTerima" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Verifikasi Terima</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Karyawan menyetor <b id="lbl_jml_setor">0</b> Pcs.</p>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Diterima (Lolos QC)</label>
                        <input type="number" id="input_diterima" class="form-control fw-bold text-success" min="0">
                        <small class="text-muted">Jika ada barang reject/rusak, kurangi jumlah ini.</small>
                    </div>
                    <input type="hidden" id="hidden_id_progres">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="submitTerima()">Simpan & Masukkan Stok</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        document.addEventListener('DOMContentLoaded', () => {
            if (!token) {
                alert("Sesi habis. Silakan login ulang.");
                window.location.href = '/login';
                return;
            }
            loadVerifikasi();
        });

        async function loadVerifikasi() {
            const tbody = document.getElementById('tableData');

            // Safety check element
            if (!tbody) return;

            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';

            try {
                const res = await fetch('/api/progres-produksi/pending', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const json = await res.json();

                // Cek apakah data ada
                if (json.data && json.data.length > 0) {
                    tbody.innerHTML = '';

                    json.data.forEach(item => {
                        // --- PERBAIKAN DI SINI (SESUAI CONTROLLER BARU) ---

                        // 1. Ambil ID Progres
                        const idProgres = item.id_progres;

                        // 2. Ambil Jumlah (Perhatikan nama key-nya: jumlah_setor)
                        const jumlah = item.jumlah_setor;

                        // 3. Ambil Nama Karyawan (Controller sudah mengirim string nama, bukan object)
                        const namaKaryawan = item.karyawan;

                        // 4. Ambil Produk & Size (Controller sudah mengirim string, bukan object)
                        const namaProduk = item.produk;
                        const namaSize = item.size;

                        // 5. Format Waktu (Karena Controller kirim raw date string)
                        // Kita format ulang di JS biar cantik
                        const dateObj = new Date(item.waktu);
                        const waktuStr = dateObj.toLocaleDateString('id-ID', {
                            day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
                        }).replace('.', ':'); // Ganti pemisah jam

                        // Render HTML
                        tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 small text-muted">${waktuStr}</td>
                            <td class="fw-bold text-dark">${namaKaryawan}</td>
                            <td>
                                <div class="fw-bold">${namaProduk}</div>
                                <span class="badge bg-light text-dark border">Size: ${namaSize}</span>
                            </td>
                            <td class="text-center fw-bold text-primary" style="font-size: 1.2em;">${jumlah}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-danger btn-sm me-1" onclick="reject('${idProgres}')" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button class="btn btn-success btn-sm" onclick="openModalTerima('${idProgres}', ${jumlah})" title="Terima">
                                    <i class="fas fa-check"></i> Proses
                                </button>
                            </td>
                        </tr>
                    `;
                    });
                } else {
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted m-0">Tidak ada setoran yang menunggu verifikasi.</p>
                        </td>
                    </tr>`;
                }
            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data.</td></tr>`;
            }
        }

        function openModalTerima(id, jumlah) {
            document.getElementById('hidden_id_progres').value = id;
            document.getElementById('lbl_jml_setor').innerText = jumlah;
            document.getElementById('input_diterima').value = jumlah;
            new bootstrap.Modal(document.getElementById('modalTerima')).show();
        }

        async function submitTerima() {
            const id = document.getElementById('hidden_id_progres').value;
            const jumlahDiterima = document.getElementById('input_diterima').value;

            if (jumlahDiterima < 0 || jumlahDiterima === '') {
                alert("Jumlah tidak valid!"); return;
            }

            // Efek Loading pada tombol (Optional, biar user tau sedang proses)
            const btnSimpan = document.querySelector('#modalTerima .btn-success');
            const textAsli = btnSimpan.innerText;
            btnSimpan.innerText = 'Menyimpan...';
            btnSimpan.disabled = true;

            try {
                const res = await fetch(`/api/progres-produksi/${id}/konfirmasi`, {
                    method: 'POST', // <--- WAJIB ADA
                    headers: {
                        'Content-Type': 'application/json', // <--- WAJIB ADA
                        'Authorization': 'Bearer ' + token  // <--- WAJIB ADA
                    },
                    body: JSON.stringify({
                        action: 'approve',
                        jumlah_diterima: jumlahDiterima
                    })
                });

                // Cek response teks dulu untuk jaga-jaga kalau errornya HTML (bukan JSON)
                const textResponse = await res.text();

                let json;
                try {
                    json = JSON.parse(textResponse);
                } catch (err) {
                    console.error("Server Error HTML:", textResponse);
                    throw new Error("Terjadi kesalahan di server (Cek Console).");
                }

                if (res.ok) {
                    // Tutup Modal Dulu
                    const modalEl = document.getElementById('modalTerima');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    alert("Berhasil! Stok bertambah.");
                    loadVerifikasi(); // Refresh tabel
                } else {
                    alert("Gagal: " + (json.message || "Error server"));
                    // Jika gagal, tetap refresh agar data terbaru (misal sudah diproses orang lain) muncul
                    loadVerifikasi();
                }
            } catch (e) {
                alert("Error: " + e.message);
            } finally {
                // Kembalikan tombol seperti semula
                btnSimpan.innerText = textAsli;
                btnSimpan.disabled = false;
            }
        }

        // --- LOGIKA TOLAK ---
        async function reject(id) {
            if(!confirm('Yakin ingin menolak setoran ini? Stok TIDAK akan bertambah.')) return;

            try {
                const res = await fetch(`/api/progres-produksi/${id}/konfirmasi`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        action: 'reject',
                        jumlah_diterima: 0
                    })
                });

                // --- TAMBAHAN PENTING ---
                const json = await res.json(); // Baca response dulu

                if (res.ok) {
                    alert("Laporan berhasil ditolak.");
                    loadVerifikasi(); // <--- WAJIB: Refresh tabel agar data hilang dari layar
                } else {
                    alert("Gagal: " + json.message);
                    loadVerifikasi(); // Refresh juga kalau gagal, biar data status terbaru muncul
                }
            } catch (e) {
                alert("Error koneksi");
            }
        }
    </script>
@endpush

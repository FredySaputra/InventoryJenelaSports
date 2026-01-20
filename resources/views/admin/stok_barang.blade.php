@extends('layouts.admin')

@section('title', 'Stok Barang')
@section('header-title', 'Matrix Stok Barang')

@section('content')

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark m-0">Input Stok</h3>
            <p class="text-muted small">Kelola jumlah stok per ukuran/varian.</p>
        </div>
        <div>
            <button class="btn btn-danger btn-sm me-2" onclick="downloadPdf()">
                <i class="fas fa-file-pdf me-1"></i> Cetak Laporan PDF
            </button>

            <button class="btn btn-primary btn-sm" onclick="loadData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    <div id="mainContainer">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p>Memuat matrix stok...</p>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
        });

        async function loadData() {
            // 1. Cari elemen container (Penyebab error sebelumnya jika ini tidak ketemu)
            const container = document.getElementById('mainContainer');
            if (!container) {
                console.error("ERROR FATAL: Elemen <div id='mainContainer'> tidak ditemukan di HTML.");
                return;
            }

            container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Sedang memuat data...</p></div>';

            try {
                const res = await fetch('/api/stoks', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                const json = await res.json();
                console.log("Response Server:", json); // Cek data di Console F12

                if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');

                const groupedData = json.data || [];
                container.innerHTML = '';

                // Jika data kosong
                if (groupedData.length === 0) {
                    container.innerHTML = `
                    <div class="alert alert-warning text-center p-5">
                        <h4>Data Kosong</h4>
                        <p>Pastikan Anda sudah membuat Kategori, Size, dan Produk.</p>
                    </div>`;
                    return;
                }

                // Loop per Kategori
                groupedData.forEach(group => {
                    const sizes = group.sizes || [];
                    const produks = group.produks || [];

                    // Skip render jika kategori ini kosong melompong
                    if (sizes.length === 0 && produks.length === 0) return;

                    // A. Header Kolom (Size)
                    let thSizes = '';
                    if (sizes.length > 0) {
                        sizes.forEach(size => {
                            thSizes += `<th class="text-center bg-light" style="width: 80px;">${size.tipe}</th>`;
                        });
                    } else {
                        thSizes = `<th class="text-center bg-light text-danger">Belum ada Size</th>`;
                    }

                    // B. Baris (Produk)
                    let trProduks = '';
                    if (produks.length > 0) {
                        produks.forEach(prod => {
                            let tdInputs = '';

                            if (sizes.length > 0) {
                                sizes.forEach(size => {
                                    // Cari Stok (Support camelCase idSize & snake_case id_size)
                                    const foundStok = (prod.stoks || []).find(s => {
                                        return (s.idSize == size.id) || (s.id_size == size.id);
                                    });

                                    const jumlah = foundStok ? foundStok.stok : 0;

                                    tdInputs += `
                                    <td class="p-1">
                                        <input type="number" min="0" class="form-control form-control-sm text-center fw-bold input-stok"
                                            value="${jumlah}"
                                            data-prod="${prod.id}"
                                            data-size="${size.id}"
                                            onchange="updateStok(this)">
                                    </td>`;
                                });
                            } else {
                                tdInputs = `<td class="text-center text-muted">-</td>`;
                            }

                            trProduks += `
                            <tr>
                                <td class="fw-bold ps-3 text-dark">
                                    ${prod.nama_lengkap}
                                    <div class="text-muted small" style="font-size:0.7rem;">ID: ${prod.id}</div>
                                </td>
                                ${tdInputs}
                            </tr>`;
                        });
                    } else {
                        trProduks = `<tr><td colspan="${sizes.length + 1}" class="text-center py-4 text-muted fst-italic">Belum ada produk di kategori ini.</td></tr>`;
                    }

                    // C. Render Card HTML
                    const cardHtml = `
                <div class="card border-0 shadow-sm mb-5 rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="m-0 fw-bold text-primary">${group.kategori_nama}</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-3 bg-white" style="min-width: 250px;">Nama Produk</th>
                                    ${thSizes}
                                </tr>
                            </thead>
                            <tbody>${trProduks}</tbody>
                        </table>
                    </div>
                </div>`;

                    container.insertAdjacentHTML('beforeend', cardHtml);
                });

            } catch (e) {
                console.error(e);
                container.innerHTML = `
                <div class="alert alert-danger text-center m-4">
                    <h5 class="fw-bold">Terjadi Kesalahan</h5>
                    <p>${e.message}</p>
                </div>`;
            }
        }

        async function updateStok(el) {
            const idProduk = el.getAttribute('data-prod');
            const idSize = el.getAttribute('data-size');
            const jumlah = el.value;

            el.classList.add('border-warning'); // Efek loading kuning

            try {
                const res = await fetch('/api/stoks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ idProduk, idSize, jumlah })
                });

                if(res.ok) {
                    el.classList.remove('border-warning');
                    el.classList.add('border-success'); // Sukses hijau
                    setTimeout(() => el.classList.remove('border-success'), 1000);
                } else {
                    throw new Error('Gagal simpan');
                }
            } catch(e) {
                el.classList.remove('border-warning');
                el.classList.add('border-danger'); // Gagal merah
                alert('Gagal update stok. Cek koneksi.');
            }
        }

        async function downloadPdf() {
            // Ubah tombol jadi loading
            const btn = document.querySelector('button[onclick="downloadPdf()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            try {
                const res = await fetch('/api/stoks/export-pdf', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token, // Token Auth wajib ada
                    }
                });

                if (!res.ok) throw new Error("Gagal download PDF");

                // Proses mengubah response menjadi File Download (Blob)
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = "Laporan_Stok_Jenela_Sports.pdf"; // Nama file download
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

            } catch (error) {
                alert("Gagal mencetak PDF. Silakan coba lagi.");
                console.error(error);
            } finally {
                // Kembalikan tombol seperti semula
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>

    <style>
        /* Styling tambahan agar input terlihat bersih */
        .input-stok:focus {
            background-color: #f0f9ff;
            border-color: #0d6efd;
            box-shadow: none;
        }
        .input-stok::-webkit-outer-spin-button,
        .input-stok::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush

@extends('layouts.admin')
@section('title', 'Verifikasi Produksi')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <strong>Konfirmasi Hasil Kerja</strong><br>
                    Data yang disetujui di sini akan otomatis <b>menambah stok barang</b> dan menghitung gaji/progres karyawan.
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold text-danger py-3">
                    <i class="fas fa-clock me-1"></i> Menunggu Konfirmasi (Pending)
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                        <tr>
                            <th class="ps-4">Karyawan</th>
                            <th>Barang & Size</th>
                            <th>Jml Setor</th>
                            <th>Waktu Setor</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="pendingBody">
                        <tr><td colspan="5" class="text-center py-4">Tidak ada data pending.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', loadPending);

        async function loadPending() {
            const res = await fetchAPI('/api/progres-produksi/pending');
            const json = await res.json();
            const tbody = document.getElementById('pendingBody');

            if(json.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data yang perlu diverifikasi.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            json.data.forEach(item => {
                // Format Barang: Nama + Warna + Bahan
                const produk = item.detail.produk;
                const namaLengkap = `${produk.nama} ${produk.warna || ''}`;

                tbody.innerHTML += `
                <tr>
                    <td class="ps-4 fw-bold">${item.karyawan.nama}</td>
                    <td>
                        ${namaLengkap}<br>
                        <span class="badge bg-light text-dark border">Size: ${item.detail.size.tipe}</span>
                    </td>
                    <td class="fs-5 fw-bold text-primary">${item.jumlah_disetor} pcs</td>
                    <td class="small text-muted">${new Date(item.waktu_setor).toLocaleString()}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-success btn-sm me-1" onclick="approve(${item.id}, ${item.jumlah_disetor})">
                            <i class="fas fa-check"></i> Terima
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="reject(${item.id})">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </td>
                </tr>
            `;
            });
        }

        async function approve(id, jumlahDisetor) {
            // Tanya admin, berapa yang diterima (QC)
            const jumlahReal = prompt("Jumlah yang lolos QC (Diterima):", jumlahDisetor);

            if (jumlahReal === null) return; // Batal
            if (jumlahReal < 0 || isNaN(jumlahReal)) { alert("Jumlah tidak valid"); return; }

            // Panggil API Konfirmasi
            const payload = {
                action: 'approve',
                jumlah_diterima: jumlahReal
            };

            const res = await fetchAPI(`/api/progres-produksi/${id}/konfirmasi`, 'POST', payload);
            if(res && res.ok) {
                alert('Sukses! Stok telah ditambahkan.');
                loadPending(); // Refresh tabel
            }
        }

        async function reject(id) {
            if(!confirm("Yakin tolak laporan ini?")) return;

            const payload = {
                action: 'reject',
                jumlah_diterima: 0
            };

            const res = await fetchAPI(`/api/progres-produksi/${id}/konfirmasi`, 'POST', payload);
            if(res && res.ok) {
                loadPending();
            }
        }
    </script>
@endpush

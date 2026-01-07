@extends('layouts.admin')

@section('title', 'Data Pelanggan - Jelena Sports')
@section('header-title', 'Manajemen Pelanggan')

@section('content')

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 fw-bold text-dark">Daftar Pelanggan</h5>
                <small class="text-muted">Kelola data pelanggan/toko rekanan.</small>
            </div>
            <button class="btn btn-primary btn-sm fw-bold px-3" onclick="openModal()">
                <i class="fas fa-plus me-1"></i> Tambah Pelanggan
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="pelangganTable">
                <thead class="table-light text-secondary small">
                <tr>
                    <th class="ps-4 py-3">KODE (ID)</th>
                    <th class="py-3">NAMA PELANGGAN</th>
                    <th class="py-3">KONTAK</th>
                    <th class="py-3">ALAMAT</th>
                    <th class="text-end pe-4 py-3">AKSI</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm mb-2"></div>
                        <br>Memuat data...
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')

    <div class="modal fade" id="pelangganModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="pelangganForm">
                        <input type="hidden" id="isEditMode" value="false">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kode Pelanggan (ID) <span class="text-danger">*</span></label>
                            <input type="text" id="id_pelanggan" class="form-control" placeholder="Contoh: PLG-001" required>
                            <div id="idHelp" class="form-text text-warning d-none">
                                <i class="fas fa-info-circle"></i> ID tidak dapat diubah saat mode edit.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" id="nama" class="form-control" placeholder="Nama Toko / PT / Perorangan" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kontak (HP/WA)</label>
                            <input type="text" id="kontak" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Lengkap</label>
                            <textarea id="alamat" class="form-control" rows="3" placeholder="Jl. Raya..."></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold" id="btnSimpan">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('api_token');
        let modalInstance; // Variabel untuk Bootstrap Modal

        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi Modal Bootstrap
            modalInstance = new bootstrap.Modal(document.getElementById('pelangganModal'));
            loadPelanggans();
        });

        // 1. LOAD DATA
        async function loadPelanggans() {
            const tableBody = document.querySelector('#pelangganTable tbody');

            try {
                const res = await fetch('/api/pelanggans', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                const json = await res.json();
                const data = json.data || [];

                tableBody.innerHTML = '';

                if(data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted fst-italic bg-light">
                                Belum ada data pelanggan.
                            </td>
                        </tr>`;
                    return;
                }

                data.forEach(item => {
                    tableBody.innerHTML += `
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-white text-primary border border-primary-subtle px-2 py-1">
                                ${item.id}
                            </span>
                        </td>
                        <td class="fw-bold text-dark">${item.nama}</td>
                        <td>${item.kontak || '<span class="text-muted">-</span>'}</td>
                        <td class="small text-muted" style="max-width: 300px;">${item.alamat || '-'}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-warning btn-sm me-1 shadow-sm" onclick="editPelanggan('${item.id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm shadow-sm" onclick="deletePelanggan('${item.id}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                });
            } catch (err) {
                console.error(err);
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Gagal memuat data pelanggan.</td></tr>`;
            }
        }

        // 2. BUKA MODAL TAMBAH
        function openModal() {
            document.getElementById('pelangganForm').reset();
            document.getElementById('isEditMode').value = "false";
            document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
            document.getElementById('btnSimpan').innerText = 'Simpan Data';

            // Reset Input ID
            const idInput = document.getElementById('id_pelanggan');
            idInput.readOnly = false;
            idInput.classList.remove('bg-light');
            document.getElementById('idHelp').classList.add('d-none');

            modalInstance.show();
            setTimeout(() => idInput.focus(), 500);
        }

        // 3. BUKA MODAL EDIT
        async function editPelanggan(id) {
            try {
                // Tampilkan loading di tombol (opsional) atau langsung fetch
                const res = await fetch(`/api/pelanggans/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const json = await res.json();

                if(res.ok) {
                    const data = json.data;

                    document.getElementById('isEditMode').value = "true";
                    document.getElementById('modalTitle').innerText = 'Edit Pelanggan';
                    document.getElementById('btnSimpan').innerText = 'Update Data';

                    // Isi Form
                    const idInput = document.getElementById('id_pelanggan');
                    idInput.value = data.id;
                    idInput.readOnly = true; // Kunci ID
                    idInput.classList.add('bg-light');
                    document.getElementById('idHelp').classList.remove('d-none');

                    document.getElementById('nama').value = data.nama;
                    document.getElementById('kontak').value = data.kontak || '';
                    document.getElementById('alamat').value = data.alamat || '';

                    modalInstance.show();
                }
            } catch (err) {
                alert('Gagal mengambil data pelanggan.');
            }
        }

        // 4. SIMPAN DATA
        document.getElementById('pelangganForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSimpan');
            const originalText = btn.innerText;

            const isEdit = document.getElementById('isEditMode').value === "true";
            const idValue = document.getElementById('id_pelanggan').value;

            // Tentukan URL & Method
            const url = isEdit ? `/api/pelanggans/${idValue}` : '/api/pelanggans';
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                id: idValue, // Diabaikan controller saat update (guarded/request), tapi wajib saat create
                nama: document.getElementById('nama').value,
                kontak: document.getElementById('kontak').value,
                alamat: document.getElementById('alamat').value,
            };

            btn.disabled = true; btn.innerText = 'Menyimpan...';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();

                if(res.ok) {
                    modalInstance.hide();
                    loadPelanggans();
                } else {
                    let msg = json.message || 'Gagal menyimpan.';
                    if(json.errors) msg += ' ' + JSON.stringify(json.errors);
                    alert(msg);
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            } finally {
                btn.disabled = false; btn.innerText = originalText;
            }
        });

        // 5. HAPUS DATA
        async function deletePelanggan(id) {
            if(!confirm(`Yakin hapus pelanggan ID: ${id}?`)) return;

            try {
                const res = await fetch(`/api/pelanggans/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if(res.ok) {
                    loadPelanggans();
                } else {
                    alert('Gagal menghapus data.');
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            }
        }
    </script>
@endpush

@extends('layouts.admin')

@section('title', 'Manajemen Size')
@section('header-title', 'Master Data Size')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Daftar Ukuran (Size)</h3>
        <p class="text-muted small">Data dikelompokkan berdasarkan Kategori Produk.</p>
    </div>

    <div id="mainContainer">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p>Memuat data...</p>
        </div>
    </div>

@endsection

@push('scripts')

    <div class="modal fade" id="modalSize" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Size</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSize">

                        <input type="hidden" id="hidden_kategori_id">

                        <div id="errorAlert" class="alert alert-danger d-none p-2 small"></div>

                        <div class="alert alert-light border mb-3 p-2 small text-muted">
                            <i class="fas fa-info-circle me-1"></i> Menambahkan ke kategori: <strong id="infoKategoriNama">-</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">ID Size <span class="text-danger">*</span></label>

                            <input type="text" id="id_size" class="form-control" placeholder="Contoh: BJU-XL" required>

                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                Ketik ID lengkap secara manual. Harus unik.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tipe (Label) <span class="text-danger">*</span></label>
                            <input type="text" id="tipe_size" class="form-control" placeholder="Contoh: XL, L, All Size" required>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Panjang (cm)</label>
                                    <input type="number" step="0.01" id="panjang_size" class="form-control" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Lebar (cm)</label>
                                    <input type="number" step="0.01" id="lebar_size" class="form-control" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary fw-bold" id="btnSimpan">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('api_token');
        let isEditMode = false;
        let currentId = null; // Menyimpan ID Size yang sedang diedit
        let modalInstance;

        document.addEventListener('DOMContentLoaded', () => {
            modalInstance = new bootstrap.Modal(document.getElementById('modalSize'));
            loadData();
        });

        // 1. LOAD DATA
        async function loadData() {
            const container = document.getElementById('mainContainer');

            try {
                const res = await fetch('/api/sizes', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error('Gagal memuat data');

                const json = await res.json();
                const groupedData = json.data || [];

                container.innerHTML = '';

                if (groupedData.length === 0) {
                    container.innerHTML = `<div class="text-center py-5 text-muted">Belum ada Kategori.</div>`;
                    return;
                }

                groupedData.forEach(group => {
                    let rows = '';

                    if(group.sizes && group.sizes.length > 0) {
                        group.sizes.forEach(item => {
                            const p = item.panjang ? item.panjang : '-';
                            const l = item.lebar ? item.lebar : '-';

                            rows += `
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-white text-dark border fw-bold px-2">${item.id}</span>
                                </td>
                                <td class="fw-bold text-primary">${item.tipe}</td>
                                <td class="text-center">${p}</td>
                                <td class="text-center">${l}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-light btn-sm border me-1 text-warning"
                                        onclick="openModal('edit', '${group.kategori_id}', '${group.kategori_nama}', '${item.id}', '${item.tipe}', '${item.panjang||''}', '${item.lebar||''}')">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm border text-danger" onclick="deleteData('${item.id}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            `;
                        });
                    } else {
                        rows = `<tr><td colspan="5" class="text-center py-4 text-muted fst-italic bg-light">Belum ada size di kategori ini.</td></tr>`;
                    }

                    const cardHtml = `
                    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0 fw-bold text-dark">${group.kategori_nama}</h5>
                                <small class="text-muted" style="font-size:0.75rem;">ID Kategori: ${group.kategori_id || 'N/A'}</small>
                            </div>

                            <button class="btn btn-primary btn-sm fw-bold px-3 shadow-sm" onclick="openModal('add', '${group.kategori_id}', '${group.kategori_nama}')">
                                <i class="fas fa-plus me-1"></i> Tambah Size
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4 py-2">ID SIZE</th>
                                        <th class="py-2">TIPE</th>
                                        <th class="text-center py-2">PANJANG</th>
                                        <th class="text-center py-2">LEBAR</th>
                                        <th class="text-end pe-4 py-2">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>
                    `;
                    container.insertAdjacentHTML('beforeend', cardHtml);
                });

            } catch (e) {
                console.error(e);
                container.innerHTML = `<div class="alert alert-danger text-center m-4">Gagal memuat data.</div>`;
            }
        }

        // 2. BUKA MODAL
        function openModal(mode, kategoriId, kategoriNama, idSize = '', tipe = '', panjang = '', lebar = '') {
            const title = document.getElementById('modalTitle');
            const inputId = document.getElementById('id_size');
            const errorAlert = document.getElementById('errorAlert');
            const infoKat = document.getElementById('infoKategoriNama');

            // Reset Form
            document.getElementById('formSize').reset();
            errorAlert.classList.add('d-none');

            // Set Kategori (Hidden & Visual)
            document.getElementById('hidden_kategori_id').value = kategoriId;
            infoKat.innerText = kategoriNama || 'Tanpa Kategori';

            modalInstance.show();

            if (mode === 'edit') {
                isEditMode = true;
                currentId = idSize;
                title.innerText = 'Edit Size';

                inputId.value = idSize;
                inputId.disabled = true; // ID tidak bisa diedit
                inputId.classList.add('bg-light');

                document.getElementById('tipe_size').value = tipe;
                document.getElementById('panjang_size').value = panjang;
                document.getElementById('lebar_size').value = lebar;
            } else {
                isEditMode = false;
                currentId = null;
                title.innerText = 'Tambah Size Baru';

                inputId.value = ''; // User ketik manual full ID
                inputId.disabled = false;
                inputId.classList.remove('bg-light');

                // Opsional: Focus ke input ID
                setTimeout(() => inputId.focus(), 500);
            }
        }

        // 3. SIMPAN DATA
        document.getElementById('formSize').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('btnSimpan');
            const originalText = btn.innerText;
            const errorAlert = document.getElementById('errorAlert');

            // Payload
            let payload = {
                id: document.getElementById('id_size').value, // ID Full Manual
                tipe: document.getElementById('tipe_size').value,
                panjang: document.getElementById('panjang_size').value || null,
                lebar: document.getElementById('lebar_size').value || null,
                idKategori: document.getElementById('hidden_kategori_id').value // Kunci Relasi
            };

            let url = '/api/sizes';
            let method = 'POST';

            if (isEditMode) {
                url += '/' + currentId;
                method = 'PUT';
                delete payload.id; // ID tidak dikirim saat update
            }

            console.log("Mengirim Payload:", payload);

            btn.innerText = 'Menyimpan...';
            btn.disabled = true;

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

                if (!res.ok) {
                    let msg = json.message || 'Gagal menyimpan.';
                    if(json.errors) {
                        // Tampilkan error validasi
                        msg += '<br>';
                        Object.keys(json.errors).forEach(key => {
                            msg += `- ${json.errors[key][0]}<br>`;
                        });
                    }
                    errorAlert.innerHTML = msg;
                    errorAlert.classList.remove('d-none');
                } else {
                    modalInstance.hide();
                    loadData();
                }
            } catch (e) {
                errorAlert.innerText = 'Gagal terhubung ke server (Koneksi Error).';
                errorAlert.classList.remove('d-none');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });

        // 4. HAPUS DATA
        async function deleteData(id) {
            if (!confirm('Yakin ingin menghapus Size ID: ' + id + '?')) return;
            try {
                const res = await fetch('/api/sizes/' + id, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if(res.ok) loadData();
                else alert('Gagal menghapus data');
            } catch (e) { alert('Kesalahan koneksi'); }
        }
    </script>
@endpush

@extends('layouts.admin')

@section('title', 'Manajemen Produk')
@section('header-title', 'Master Data Produk')

@section('content')

    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Daftar Produk</h3>
        <p class="text-muted small">Produk dikelompokkan berdasarkan Kategori. Nama produk otomatis digabung dengan Warna dan Bahan.</p>
    </div>

    <div id="mainContainer">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p>Memuat data produk...</p>
        </div>
    </div>

@endsection

@push('scripts')

    <div class="modal fade" id="modalProduk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formProduk">

                        <input type="hidden" id="hidden_kategori_id">

                        <div id="errorAlert" class="alert alert-danger d-none p-2 small"></div>

                        <div class="alert alert-light border mb-3 p-2 small text-muted">
                            <i class="fas fa-tag me-1"></i> Menambahkan ke Kategori: <strong id="infoKategoriNama" class="text-primary">-</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kode Produk (ID) <span class="text-danger">*</span></label>
                            <input type="text" id="prod_id" class="form-control" placeholder="Contoh: RNG-01" required>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">ID harus unik dan tidak boleh ada spasi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" id="prod_nama" class="form-control" placeholder="Contoh: Baju Renang" required>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Cukup nama inti saja (Tanpa warna/bahan).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Warna <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" id="prod_warna" class="form-control" placeholder="Contoh: Merah, Hitam Polos">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Bahan <span class="text-danger">*</span></label>
                            <select id="prod_bahan" class="form-select" required>
                                <option value="">-- Pilih Bahan --</option>
                            </select>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                * Pilihan bahan muncul otomatis sesuai kategori ini.
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold" id="btnSimpan">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('api_token');
        let isEditMode = false;
        let currentId = null;
        let modalInstance;

        document.addEventListener('DOMContentLoaded', () => {
            modalInstance = new bootstrap.Modal(document.getElementById('modalProduk'));
            loadData();
        });

        async function loadData() {
            const container = document.getElementById('mainContainer');

            try {
                const res = await fetch('/api/produks', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error('Gagal memuat data');

                const json = await res.json();
                const groupedData = json.data || [];

                container.innerHTML = '';

                if (groupedData.length === 0) {
                    container.innerHTML = `<div class="text-center py-5 text-muted">Belum ada Kategori/Produk.</div>`;
                    return;
                }

                groupedData.forEach(group => {
                    let rows = '';

                    if(group.produks && group.produks.length > 0) {
                        group.produks.forEach(prod => {

                            const displayName = prod.nama_lengkap || prod.nama;

                            rows += `
                            <tr>
                                <td class="ps-4" style="width: 20%;">
                                    <span class="badge bg-white text-dark border fw-bold px-2">
                                        ${prod.id}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">
                                    ${displayName}
                                </td>
                                <td class="text-end pe-4" style="width: 20%;">
                                    <button class="btn btn-warning btn-sm me-1"
                                        onclick="openModal('edit', '${group.id}', '${group.nama}',
                                        '${prod.id}', '${prod.nama}', '${prod.warna || ''}', '${prod.bahan_id || ''}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <button class="btn btn-danger btn-sm" onclick="deleteProduk('${prod.id}')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            `;
                        });
                    } else {
                        rows = `<tr><td colspan="3" class="text-center py-4 text-muted fst-italic bg-light">Belum ada produk di kategori ini.</td></tr>`;
                    }

                    const cardHtml = `
                    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0 fw-bold text-dark">${group.nama}</h5>
                                <small class="text-muted" style="font-size:0.75rem;">ID Kategori: ${group.id}</small>
                            </div>

                            <button class="btn btn-primary btn-sm fw-bold px-3" onclick="openModal('add', '${group.id}', '${group.nama}')">
                                <i class="fas fa-plus me-1"></i> Tambah Produk
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4 py-2">KODE (ID)</th>
                                        <th class="py-2">NAMA PRODUK LENGKAP</th>
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

        async function openModal(mode, kategoriId, kategoriNama, idProduk = '', namaProduk = '', warna = '', idBahan = '') {
            document.getElementById('formProduk').reset();
            document.getElementById('errorAlert').classList.add('d-none');

            document.getElementById('hidden_kategori_id').value = kategoriId;
            document.getElementById('infoKategoriNama').innerText = kategoriNama;

            const title = document.getElementById('modalTitle');
            const inputId = document.getElementById('prod_id');
            const inputNama = document.getElementById('prod_nama');
            const inputWarna = document.getElementById('prod_warna');
            const selectBahan = document.getElementById('prod_bahan');

            await loadBahanOptions(kategoriId, idBahan);

            modalInstance.show();

            if (mode === 'edit') {
                isEditMode = true;
                currentId = idProduk;
                title.innerText = 'Edit Produk';

                inputId.value = idProduk;
                inputId.disabled = true;
                inputId.classList.add('bg-light');

                inputNama.value = namaProduk;
                inputWarna.value = warna;
                selectBahan.value = idBahan;
            } else {
                isEditMode = false;
                currentId = null;
                title.innerText = 'Tambah Produk';

                inputId.value = '';
                inputId.disabled = false;
                inputId.classList.remove('bg-light');
            }
        }

        async function loadBahanOptions(kategoriId, selectedBahanId = '') {
            const selectBahan = document.getElementById('prod_bahan');
            selectBahan.innerHTML = '<option value="">Memuat bahan...</option>';
            selectBahan.disabled = true;

            try {
                const res = await fetch(`/api/bahans`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if(res.ok) {
                    const json = await res.json();
                    const allBahan = json.data || [];

                    const filteredBahan = allBahan.filter(b => b.idKategori === kategoriId);

                    selectBahan.innerHTML = '<option value="">-- Pilih Bahan --</option>';

                    if(filteredBahan.length > 0) {
                        filteredBahan.forEach(bhn => {
                            const isSelected = bhn.id == selectedBahanId ? 'selected' : '';
                            selectBahan.innerHTML += `<option value="${bhn.id}" ${isSelected}>${bhn.nama}</option>`;
                        });
                        selectBahan.disabled = false;
                    } else {
                        selectBahan.innerHTML = '<option value="">Tidak ada bahan di kategori ini. Tambahkan bahan dulu.</option>';
                    }
                } else {
                    selectBahan.innerHTML = '<option value="">Gagal mengambil data bahan.</option>';
                }
            } catch(e) {
                console.error(e);
                selectBahan.innerHTML = '<option value="">Error Server (Bahan)</option>';
            }
        }

        document.getElementById('formProduk').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSimpan');
            const alertBox = document.getElementById('errorAlert');

            let payload = {
                nama: document.getElementById('prod_nama').value,
                warna: document.getElementById('prod_warna').value,
                idKategori: document.getElementById('hidden_kategori_id').value,
                idBahan: document.getElementById('prod_bahan').value
            };

            let url = '/api/produks';
            let method = 'POST';

            if(isEditMode) {
                url += '/' + currentId;
                method = 'PUT';
            } else {
                payload.id = document.getElementById('prod_id').value;
            }

            btn.disabled = true; btn.innerText = 'Menyimpan...';
            alertBox.classList.add('d-none');

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
                    loadData();
                } else {
                    let msg = json.message || 'Gagal menyimpan.';
                    if(json.errors) {
                        msg += '<br>';
                        Object.keys(json.errors).forEach(key => {
                            msg += `- ${json.errors[key][0]}<br>`;
                        });
                    }
                    alertBox.innerHTML = msg;
                    alertBox.classList.remove('d-none');
                }
            } catch(e) {
                alertBox.innerText = 'Error Koneksi.';
                alertBox.classList.remove('d-none');
            } finally {
                btn.disabled = false; btn.innerText = 'Simpan Produk';
            }
        });

        async function deleteProduk(id) {
            if(!confirm('Hapus produk ini?')) return;

            try {
                const res = await fetch('/api/produks/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();

                if(res.ok) {
                    loadData();
                } else {
                    alert(json.message || 'Terjadi kesalahan saat menghapus data.');
                }

            } catch(e) {
                console.error(e);
                alert('Kesalahan koneksi atau server tidak merespon.');
            }
        }
    </script>
@endpush

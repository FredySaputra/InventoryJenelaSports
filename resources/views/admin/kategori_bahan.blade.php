@extends('layouts.admin')

@section('title', 'Kategori & Bahan')
@section('header-title', 'Manajemen Kategori & Bahan')

@section('content')

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0 text-dark">Data Kategori</h5>
                    <button class="btn btn-primary btn-sm fw-bold" onclick="openModalKategori()">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Nama Kategori</th>
                                <th class="text-center" style="width: 50px;"></th>
                            </tr>
                            </thead>
                            <tbody id="listKategori">
                            <tr><td colspan="3" class="text-center py-4 text-muted">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100" style="min-height: 400px;">

                <div id="emptyStateBahan" class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="fas fa-hand-point-left fa-3x text-secondary opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pilih Kategori Dahulu</h5>
                    <p class="text-muted">Klik salah satu kategori di tabel sebelah kiri<br>untuk mengelola bahan.</p>
                </div>

                <div id="contentBahan" class="d-none">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Bahan untuk Kategori:</small>
                            <h4 class="fw-bold m-0 text-primary" id="selectedKategoriName">-</h4>
                        </div>
                        <button class="btn btn-primary btn-sm fw-bold" onclick="openModalBahan()">
                            <i class="fas fa-plus me-1"></i> Tambah Bahan
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Nama Bahan</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                                </thead>
                                <tbody id="listBahan">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formKategori">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kode Kategori (ID) <span class="text-danger">*</span></label>
                            <input type="text" id="cat_id" class="form-control" placeholder="Cth: KAT-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" id="cat_nama" class="form-control" placeholder="Cth: Baju Beladiri" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Kategori</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBahan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Menambahkan ke kategori: <strong id="modalKategoriLabel" class="text-primary">-</strong></p>
                    <form id="formBahan">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kode Bahan (ID) <span class="text-danger">*</span></label>
                            <input type="text" id="bhn_id" class="form-control" placeholder="Cth: BHN-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Bahan <span class="text-danger">*</span></label>
                            <input type="text" id="bhn_nama" class="form-control" placeholder="Cth: Drill / Canvas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
                            <textarea id="bhn_deskripsi" class="form-control" rows="3" placeholder="Keterangan bahan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Bahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('api_token');
        let currentKategoriId = null;
        let currentKategoriName = null;

        // Instance Modal Bootstrap
        let modalKatInstance;
        let modalBahanInstance;

        document.addEventListener('DOMContentLoaded', () => {
            modalKatInstance = new bootstrap.Modal(document.getElementById('modalKategori'));
            modalBahanInstance = new bootstrap.Modal(document.getElementById('modalBahan'));
            loadKategoris();
        });

        // --- 1. LOAD KATEGORI ---
        async function loadKategoris() {
            try {
                const res = await fetch('/api/kategoris', { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                const data = json.data;

                const list = document.getElementById('listKategori');
                list.innerHTML = '';

                if(!data || data.length === 0) {
                    list.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted">Belum ada kategori.</td></tr>`;
                    return;
                }

                data.forEach(item => {
                    // Logic Active State
                    const isActive = (currentKategoriId === item.id);
                    const activeClass = isActive ? 'table-primary border-start border-4 border-primary' : '';
                    const iconAction = isActive ? '<i class="fas fa-chevron-right text-primary"></i>' : '';

                    list.innerHTML += `
                    <tr class="${activeClass}" style="cursor:pointer; transition:0.2s;" onclick="selectKategori('${item.id}', '${item.nama}')">
                        <td class="ps-3 fw-bold small text-muted">${item.id}</td>
                        <td class="fw-bold text-dark">${item.nama}</td>
                        <td class="text-center">
                            ${iconAction}
                            <button class="btn btn-link text-danger p-0 ms-2" style="text-decoration:none;" onclick="deleteKategori(event, '${item.id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                });
            } catch(e) { console.error("Gagal load kategori", e); }
        }

        // --- 2. PILIH KATEGORI (TAMPILKAN BAHAN) ---
        function selectKategori(id, nama) {
            currentKategoriId = id;
            currentKategoriName = nama;

            // Toggle Tampilan Kanan
            document.getElementById('emptyStateBahan').classList.add('d-none');
            document.getElementById('contentBahan').classList.remove('d-none');

            document.getElementById('selectedKategoriName').innerText = nama;

            // Refresh Kiri (untuk highlight) & Load Kanan
            loadKategoris();
            loadBahans(id);
        }

        async function loadBahans(kategoriId) {
            const list = document.getElementById('listBahan');
            list.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted">Memuat data bahan...</td></tr>';

            try {
                const res = await fetch(`/api/bahans/kategori/${kategoriId}?t=${new Date().getTime()}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if (!res.ok) throw new Error("Gagal mengambil data");

                const json = await res.json();
                const data = json.data;

                list.innerHTML = '';

                if(!data || data.length === 0) {
                    list.innerHTML = `<tr><td colspan="4" class="text-center py-5 text-muted fst-italic">Belum ada bahan di kategori ini.</td></tr>`;
                    return;
                }

                data.forEach(item => {
                    const deskripsi = item.deskripsi ? item.deskripsi : '<span class="text-muted">-</span>';

                    list.innerHTML += `
                    <tr>
                        <td class="ps-3"><span class="badge bg-secondary">${item.id}</span></td>
                        <td class="fw-bold">${item.nama}</td>
                        <td class="small text-muted">${deskripsi}</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-danger btn-sm" onclick="deleteBahan('${item.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                });
            } catch(e) {
                console.error(e);
                list.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            }
        }

        // --- 3. PROSES SIMPAN KATEGORI ---
        document.getElementById('formKategori').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('cat_id').value,
                nama: document.getElementById('cat_nama').value
            };

            const btn = e.target.querySelector('button[type="submit"]');
            const oriText = btn.innerText;
            btn.innerText = 'Menyimpan...'; btn.disabled = true;

            try {
                const res = await fetch('/api/kategoris', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    modalKatInstance.hide();
                    loadKategoris();
                    alert('Kategori berhasil ditambahkan');
                } else {
                    const json = await res.json();
                    alert('Gagal: ' + (json.message || 'Cek ID duplikat'));
                }
            } catch(e) { alert('Error sistem'); }
            finally { btn.innerText = oriText; btn.disabled = false; }
        });

        // --- 4. PROSES SIMPAN BAHAN ---
        document.getElementById('formBahan').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('bhn_id').value,
                nama: document.getElementById('bhn_nama').value,
                deskripsi: document.getElementById('bhn_deskripsi').value || '-',
                idKategori: currentKategoriId
            };

            const btn = e.target.querySelector('button[type="submit"]');
            const oriText = btn.innerText;
            btn.innerText = 'Menyimpan...'; btn.disabled = true;

            try {
                const res = await fetch('/api/bahans', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    modalBahanInstance.hide();
                    loadBahans(currentKategoriId);
                    alert('Bahan berhasil ditambahkan');
                } else {
                    const json = await res.json();
                    alert('Gagal: ' + (json.message || 'Error'));
                }
            } catch(e) { alert('Error sistem'); }
            finally { btn.innerText = oriText; btn.disabled = false; }
        });

        // --- 5. FUNGSI DELETE & MODAL TRIGGER ---
        async function deleteKategori(event, id) {
            event.stopPropagation(); // Mencegah klik row terpanggil
            if(!confirm('Hapus Kategori? Pastikan kategori ini kosong!')) return;

            const res = await fetch(`/api/kategoris/${id}`, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });

            if(res.ok) {
                if(currentKategoriId == id) {
                    // Reset View Kanan jika yg dihapus adalah yg sedang aktif
                    document.getElementById('emptyStateBahan').classList.remove('d-none');
                    document.getElementById('contentBahan').classList.add('d-none');
                    currentKategoriId = null;
                }
                loadKategoris();
            } else {
                alert('Gagal menghapus kategori (Mungkin masih ada isinya)');
            }
        }

        async function deleteBahan(id) {
            if(!confirm('Hapus Bahan ini?')) return;
            await fetch(`/api/bahans/${id}`, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });
            loadBahans(currentKategoriId);
        }

        function openModalKategori() {
            document.getElementById('formKategori').reset();
            modalKatInstance.show();
        }

        function openModalBahan() {
            if (!currentKategoriId) {
                alert("Pilih kategori terlebih dahulu.");
                return;
            }
            document.getElementById('formBahan').reset();
            document.getElementById('modalKategoriLabel').innerText = currentKategoriName;
            modalBahanInstance.show();
        }

    </script>
@endpush

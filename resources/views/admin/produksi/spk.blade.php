@extends('layouts.admin')

@section('title', 'Perintah Produksi (SPK)')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-primary">Daftar Surat Perintah Kerja</h5>
            <button class="btn btn-primary btn-sm" onclick="openModal()">
                <i class="fas fa-plus"></i> Buat SPK Baru
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No. SPK</th>
                        <th>Tanggal Target</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Total Item</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="tableBody">
                    <tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSPK" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Buat SPK Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSPK">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Target Selesai</label>
                                <input type="date" id="tgl_target" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Pelanggan (Opsional)</label>
                                <select id="selectPelanggan" class="form-select">
                                    <option value="">-- Stok Gudang --</option>
                                </select>
                            </div>
                        </div>

                        <div class="border p-3 rounded bg-light mb-3">
                            <label class="form-label small fw-bold mb-2">Item yang akan diproduksi:</label>
                            <div id="itemsContainer"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addItemRow()">
                                <i class="fas fa-plus"></i> Tambah Baris Item
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan & Terbitkan SPK</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-primary">Detail SPK: <span id="det_no_spk"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Pelanggan</small>
                            <span class="fw-bold" id="det_pelanggan">-</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Tanggal Target</small>
                            <span class="fw-bold" id="det_tanggal">-</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Status</small>
                            <span id="det_status">-</span>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2">Progress Produksi</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="bg-light text-secondary">
                            <tr>
                                <th>Nama Produk</th>
                                <th class="text-center" width="80">Size</th>
                                <th class="text-center" width="100">Target</th>
                                <th class="text-center" width="100">Selesai</th>
                                <th class="text-center" width="100">Sisa</th>
                                <th width="150">Progress</th>
                            </tr>
                            </thead>
                            <tbody id="detailBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" id="btnCancelSPK" onclick="confirmCancelSPK()">
                        <i class="fas fa-ban"></i> Batalkan SPK Ini
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');

        // --- VARIABEL GLOBAL ---
        let globalProdukMap = {};
        let globalProdukOptions = '<option value="">Gagal memuat...</option>';
        let globalSizes = [];
        let currentSpkId = null; // ID SPK yang sedang dibuka detailnya

        document.addEventListener('DOMContentLoaded', async () => {
            if (!token) {
                alert("Sesi habis. Silakan login ulang.");
                window.location.href = '/login'; return;
            }
            await loadMasterData();
            loadSPK();
            loadPelangganOptions();
        });

        // --- 1. LOAD MASTER DATA (Produk & Size) ---
        async function loadMasterData() {
            try {
                const [resProd, resSize] = await Promise.all([
                    fetch('/api/produks', { headers: { 'Authorization': 'Bearer ' + token } }),
                    fetch('/api/sizes', { headers: { 'Authorization': 'Bearer ' + token } })
                ]);
                const jsonProd = await resProd.json();
                const jsonSize = await resSize.json();

                // Produk
                if (resProd.ok) {
                    globalProdukOptions = '<option value="">-- Pilih Produk --</option>';
                    const dataKategori = jsonProd.data || [];
                    dataKategori.forEach(kat => {
                        if (kat.produks && kat.produks.length > 0) {
                            globalProdukOptions += `<optgroup label="${kat.nama}">`;
                            kat.produks.forEach(prod => {
                                globalProdukMap[prod.id] = prod.idKategori || kat.id;
                                const label = `${prod.nama} ${prod.warna||''} ${prod.bahan?prod.bahan.nama:''}`.trim();
                                globalProdukOptions += `<option value="${prod.id}">${label}</option>`;
                            });
                            globalProdukOptions += `</optgroup>`;
                        }
                    });
                }
                // Size (Flatten: Ambil dari dalam kategori)
                if (resSize.ok) {
                    const groups = jsonSize.data || [];
                    globalSizes = [];
                    groups.forEach(g => { if(g.sizes) globalSizes = globalSizes.concat(g.sizes); });
                }
                // Init Row di Modal Tambah
                const container = document.getElementById('itemsContainer');
                if(container) { container.innerHTML = ''; addItemRow(); }
            } catch (e) { console.error(e); }
        }

        // --- 2. LOAD SPK LIST (Tabel Utama) ---
        async function loadSPK() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>';
            try {
                const res = await fetch('/api/perintah-produksi', { headers: { 'Authorization': 'Bearer ' + token } });
                if(!res.ok) throw new Error("Gagal load data");
                const json = await res.json();

                tbody.innerHTML = '';
                if (json.data && json.data.length > 0) {
                    json.data.forEach(item => {
                        const pelanggan = item.pelanggan ? item.pelanggan.nama : '<span class="badge bg-secondary">Stok Gudang</span>';

                        // Hitung Total Pcs
                        let totalPcs = 0;
                        if (item.details) {
                            item.details.forEach(d => { totalPcs += parseInt(d.jumlah_target) || 0; });
                        }

                        // Warna Status
                        let statusBadge = 'bg-secondary';
                        if(item.status === 'Proses') statusBadge = 'bg-primary';
                        if(item.status === 'Selesai') statusBadge = 'bg-success';
                        if(item.status === 'Dibatalkan') statusBadge = 'bg-danger';

                        tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 fw-bold text-primary">${item.id}</td>
                            <td>${item.tanggal_target || '-'}</td>
                            <td>${pelanggan}</td>
                            <td><span class="badge ${statusBadge}">${item.status}</span></td>
                            <td class="fw-bold">${totalPcs} Pcs</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-info btn-sm text-white" onclick="showDetail('${item.id}')">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>`;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data SPK.</td></tr>';
                }
            } catch (e) { tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error memuat data.</td></tr>`; }
        }

        // --- 3. SHOW DETAIL (Tampil Modal & Tombol Batal) ---
        async function showDetail(id) {
            currentSpkId = id;
            const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
            modal.show();

            document.getElementById('det_no_spk').innerText = id;
            document.getElementById('detailBody').innerHTML = '<tr><td colspan="6" class="text-center">Mengambil data...</td></tr>';

            // Reset Tombol Batal
            const btnCancel = document.getElementById('btnCancelSPK');
            btnCancel.style.display = 'block';

            try {
                const res = await fetch(`/api/perintah-produksi/${id}`, { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                if(!res.ok) { alert("Gagal mengambil detail"); return; }
                const spk = json.data;

                // Header Info
                document.getElementById('det_pelanggan').innerText = spk.pelanggan ? spk.pelanggan.nama : 'Stok Gudang';
                document.getElementById('det_tanggal').innerText = spk.tanggal_target;

                let badgeClass = 'bg-secondary';
                if(spk.status === 'Proses') badgeClass = 'bg-primary';
                if(spk.status === 'Selesai') badgeClass = 'bg-success';
                if(spk.status === 'Dibatalkan') badgeClass = 'bg-danger';
                document.getElementById('det_status').innerHTML = `<span class="badge ${badgeClass}">${spk.status}</span>`;

                // Sembunyikan tombol batal jika sudah Selesai/Batal
                if (spk.status === 'Selesai' || spk.status === 'Dibatalkan') {
                    btnCancel.style.display = 'none';
                }

                // Isi Tabel Progress
                const tbody = document.getElementById('detailBody');
                tbody.innerHTML = '';
                spk.details.forEach(item => {
                    const sisa = item.jumlah_target - item.jumlah_selesai;
                    const percent = Math.round((item.jumlah_selesai / item.jumlah_target) * 100);

                    let barColor = 'bg-warning';
                    if(percent >= 50) barColor = 'bg-info';
                    if(percent >= 100) barColor = 'bg-success';

                    const prod = item.produk;
                    const namaFull = `${prod.nama} ${prod.warna||''} ${prod.bahan?prod.bahan.nama:''}`;

                    tbody.innerHTML += `
                    <tr>
                        <td>${namaFull}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">${item.size.tipe}</span></td>
                        <td class="text-center fw-bold">${item.jumlah_target}</td>
                        <td class="text-center text-success">${item.jumlah_selesai}</td>
                        <td class="text-center text-danger">${sisa}</td>
                        <td>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar ${barColor}" style="width: ${percent}%">${percent}%</div>
                            </div>
                        </td>
                    </tr>
                `;
                });
            } catch (e) { console.error(e); }
        }

        // --- 4. CONFIRM BATALKAN SPK ---
        async function confirmCancelSPK() {
            if(!currentSpkId) return;

            if (!confirm("PERINGATAN: Yakin batalkan SPK ini? \nStatus akan berubah jadi 'Dibatalkan'.")) {
                return;
            }

            const btn = document.getElementById('btnCancelSPK');
            btn.innerText = "Memproses..."; btn.disabled = true;

            try {
                const res = await fetch(`/api/perintah-produksi/${currentSpkId}/batal`, {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const json = await res.json();

                if (res.ok) {
                    alert("SPK Telah Dibatalkan.");
                    location.reload();
                } else {
                    alert("Gagal: " + json.message);
                }
            } catch (e) {
                alert("Error koneksi.");
            } finally {
                btn.innerText = "Batalkan SPK Ini"; btn.disabled = false;
            }
        }

        // --- FUNGSI HELPER LAIN ---
        function onProdukChange(el) {
            const row = el.closest('.item-row');
            const sizeSelect = row.querySelector('.input-size');
            const produkId = el.value;

            sizeSelect.innerHTML = '<option value="">-- Size --</option>'; sizeSelect.disabled = true;
            if (!produkId) return;

            const katId = globalProdukMap[produkId];
            // Filter Size: Tampilkan jika idKategori cocok ATAU idKategori null (size umum)
            const filtered = globalSizes.filter(s => !s.idKategori || s.idKategori === katId);

            if (filtered.length > 0) {
                filtered.forEach(s => {
                    const label = s.tipe || s.nama || s.id;
                    sizeSelect.innerHTML += `<option value="${s.id}">${label}</option>`;
                });
                sizeSelect.disabled = false;
            } else {
                sizeSelect.innerHTML = '<option value="">Tidak ada size</option>';
            }
        }

        function addItemRow() {
            const div = document.createElement('div');
            div.className = 'row g-2 mb-2 item-row align-items-center';
            div.innerHTML = `
            <div class="col-md-6"><select class="form-select input-produk" onchange="onProdukChange(this)" required>${globalProdukOptions}</select></div>
            <div class="col-md-3"><select class="form-select input-size" required disabled><option>-- Produk Dulu --</option></select></div>
            <div class="col-md-2"><input type="number" class="form-control input-qty" placeholder="Qty" required min="1"></div>
            <div class="col-md-1"><button type="button" class="btn btn-danger w-100" onclick="this.closest('.item-row').remove()">x</button></div>
        `;
            document.getElementById('itemsContainer').appendChild(div);
        }

        async function loadPelangganOptions() {
            try {
                const res = await fetch('/api/pelanggans', { headers: { 'Authorization': 'Bearer ' + token } });
                const json = await res.json();
                const sel = document.getElementById('selectPelanggan');
                if(json.data) json.data.forEach(p => sel.innerHTML += `<option value="${p.id}">${p.nama}</option>`);
            } catch(e){}
        }

        document.getElementById('formSPK').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            const rows = document.querySelectorAll('.item-row');
            let items = [];
            rows.forEach(r => {
                const p = r.querySelector('.input-produk').value, s = r.querySelector('.input-size').value, q = r.querySelector('.input-qty').value;
                if(p&&s&&q) items.push({ idProduk: p, idSize: s, jumlah_target: q });
            });
            if(items.length===0) return alert("Minimal 1 item");

            btn.disabled=true; btn.innerText="Menyimpan...";
            try {
                const res = await fetch('/api/perintah-produksi', {
                    method: 'POST', headers: {'Content-Type':'application/json','Authorization':'Bearer '+token},
                    body: JSON.stringify({
                        tanggal_target: document.getElementById('tgl_target').value,
                        idPelanggan: document.getElementById('selectPelanggan').value||null,
                        items: items
                    })
                });
                if(res.ok) { alert('Berhasil!'); location.reload(); }
                else { alert('Gagal simpan.'); }
            } catch(e) { alert('Error koneksi.'); }
            finally { btn.disabled=false; btn.innerText="Simpan & Terbitkan SPK"; }
        });

        function openModal() {
            document.getElementById('itemsContainer').innerHTML=''; addItemRow();
            new bootstrap.Modal(document.getElementById('modalSPK')).show();
        }
    </script>
@endpush

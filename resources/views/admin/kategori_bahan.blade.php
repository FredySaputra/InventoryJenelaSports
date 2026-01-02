@extends('layouts.admin')

@section('title', 'Kategori & Bahan - Jelena Sports')
@section('header-title', 'Manajemen Kategori & Bahan')

@section('content')

    <div style="display: flex; gap: 20px; align-items: flex-start;">

        <div style="flex: 35%; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; font-size: 1.1rem;">Data Kategori</h3>
                <button class="btn btn-primary" onclick="openModalKategori()" style="font-size: 0.8rem;">+ Tambah</button>
            </div>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                <tr style="background: #f1f5f9; text-align: left;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nama</th>
                    <th style="padding: 10px; width: 30px;"></th>
                </tr>
                </thead>
                <tbody id="listKategori">
                <tr><td colspan="3" align="center">Memuat...</td></tr>
                </tbody>
            </table>
        </div>

        <div style="flex: 65%; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); min-height: 400px;">

            <div id="bahanHeader" style="display:none; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                <div>
                    <small style="color: gray;">Daftar Bahan untuk Kategori:</small>
                    <h3 style="margin: 0; color: #2563eb;" id="selectedKategoriName"></h3>
                </div>
                <button class="btn btn-primary" onclick="openModalBahan()" style="font-size: 0.8rem;">+ Tambah Bahan</button>
            </div>

            <div id="emptyStateBahan" style="text-align: center; color: gray; margin-top: 50px;">
                <p style="font-size: 3rem; margin:0;">👈</p>
                <p>Pilih salah satu <b>Kategori</b> di sebelah kiri<br>untuk melihat atau menambah bahan.</p>
            </div>

            <table id="tableBahan" style="width: 100%; border-collapse: collapse; display: none;">
                <thead>
                <tr style="background: #f1f5f9; text-align: left;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nama Bahan</th>
                    <th style="padding: 10px;">Deskripsi</th>
                    <th style="padding: 10px; text-align: right;">Aksi</th>
                </tr>
                </thead>
                <tbody id="listBahan">
                </tbody>
            </table>
        </div>

    </div>

    <div id="modalKategori" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <span class="close" onclick="closeModalKategori()">&times;</span>
            <h3>Tambah Kategori</h3>
            <form id="formKategori">
                <div class="form-group">
                    <label>Kode Kategori (ID) <span style="color:red">*</span></label>
                    <input type="text" id="cat_id" placeholder="Cth: KAT-01" required maxlength="100">
                </div>
                <div class="form-group">
                    <label>Nama Kategori <span style="color:red">*</span></label>
                    <input type="text" id="cat_nama" placeholder="Cth: Baju Beladiri" required maxlength="255">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Simpan</button>
            </form>
        </div>
    </div>

    <div id="modalBahan" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <span class="close" onclick="closeModalBahan()">&times;</span>
            <h3>Tambah Bahan</h3>
            <p style="margin-top:0; font-size: 0.9rem; color:gray;">Kategori: <strong id="modalKategoriLabel"></strong></p>
            <form id="formBahan">
                <input type="hidden" id="bhn_kategori_id">

                <div class="form-group">
                    <label>Kode Bahan (ID) <span style="color:red">*</span></label>
                    <input type="text" id="bhn_id" placeholder="Cth: BHN-01" required maxlength="100">
                </div>
                <div class="form-group">
                    <label>Nama Bahan <span style="color:red">*</span></label>
                    <input type="text" id="bhn_nama" placeholder="Cth: Drill / Canvas" required maxlength="100">
                </div>
                <div class="form-group">
                    <label>Deskripsi (Opsional)</label>
                    <textarea id="bhn_deskripsi" rows="3" placeholder="Keterangan bahan..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Simpan</button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const token = localStorage.getItem('api_token');
        let currentKategoriId = null;
        let currentKategoriName = null;

        async function loadKategoris() {
            try {
                const res = await fetch('/api/kategoris', { headers: { 'Authorization': 'Bearer ' + token } });
                const data = await res.json();

                const list = document.getElementById('listKategori');
                list.innerHTML = '';

                data.forEach(item => {
                    const bg = (currentKategoriId === item.id) ? '#eff6ff' : 'white';
                    const border = (currentKategoriId === item.id) ? '2px solid #2563eb' : 'none';

                    list.innerHTML += `
                    <tr style="background:${bg}; cursor:pointer; border-left:${border}; transition:0.2s;" onclick="selectKategori('${item.id}', '${item.nama}')">
                        <td style="padding: 10px; font-weight:bold;">${item.id}</td>
                        <td style="padding: 10px;">${item.nama}</td>
                        <td style="padding: 10px; text-align:center;">
                            <button class="btn-danger" style="padding: 2px 6px; font-size:0.7rem;" onclick="deleteKategori(event, '${item.id}')">X</button>
                        </td>
                    </tr>
                `;
                });
            } catch(e) { console.error("Gagal load kategori", e); }
        }

        function selectKategori(id, nama) {
            currentKategoriId = id;
            currentKategoriName = nama;

            document.getElementById('emptyStateBahan').style.display = 'none';
            document.getElementById('bahanHeader').style.display = 'flex';
            document.getElementById('tableBahan').style.display = 'table';
            document.getElementById('selectedKategoriName').innerText = nama;

            loadKategoris();
            loadBahans(id);
        }

        async function loadBahans(kategoriId) {
            const list = document.getElementById('listBahan');
            list.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px;">Memuat data...</td></tr>';

            try {
                const res = await fetch(`/api/bahans/kategori/${kategoriId}?t=${new Date().getTime()}`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if (!res.ok) throw new Error("Gagal mengambil data");

                const responseJson = await res.json();
                const data = responseJson.data;

                list.innerHTML = '';

                if(!data || data.length === 0) {
                    list.innerHTML = '<tr><td colspan="4" style="text-align:center; color:gray; padding:20px;">Belum ada bahan untuk kategori ini.</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const deskripsi = item.deskripsi ? item.deskripsi : '<span style="color:#ccc">-</span>';

                    list.innerHTML += `
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px;"><b>${item.id}</b></td>
                        <td style="padding: 10px;">${item.nama}</td>
                        <td style="padding: 10px; font-size:0.9rem; color:#555;">${deskripsi}</td>
                        <td style="padding: 10px; text-align: right;">
                            <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteBahan('${item.id}')">Hapus</button>
                        </td>
                    </tr>
                `;
                });
            } catch(e) {
                console.error(e);
                list.innerHTML = '<tr><td colspan="4" style="text-align:center; color:red; padding:20px;">Gagal memuat data. Cek koneksi atau Console.</td></tr>';
            }
        }

        document.getElementById('formKategori').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('cat_id').value,
                nama: document.getElementById('cat_nama').value
            };

            const res = await fetch('/api/kategoris', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify(payload)
            });

            if(res.ok) {
                closeModalKategori();
                loadKategoris();
            } else {
                const json = await res.json();
                alert('Gagal: ' + (json.message || 'Cek ID duplikat'));
            }
        });

        document.getElementById('formBahan').addEventListener('submit', async (e) => {
            e.preventDefault();

            const idBahan = document.getElementById('bhn_id').value;
            const namaBahan = document.getElementById('bhn_nama').value;
            const deskripsiBahan = document.getElementById('bhn_deskripsi').value || '-';

            const payload = {
                id: idBahan,
                nama: namaBahan,
                deskripsi: deskripsiBahan,
                idKategori: currentKategoriId
            };

            const btnSimpan = e.target.querySelector('button[type="submit"]');
            const textAsli = btnSimpan.innerText;
            btnSimpan.innerText = 'Menyimpan...';
            btnSimpan.disabled = true;

            try {
                const res = await fetch('/api/bahans', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify(payload)
                });

                if(res.ok) {
                    closeModalBahan();

                    const list = document.getElementById('listBahan');

                    if(list.innerText.includes('Belum ada') || list.innerText.includes('Memuat') || list.innerText.includes('Gagal')) {
                        list.innerHTML = '';
                    }

                    const newRow = `
                    <tr style="border-bottom: 1px solid #f1f5f9; background-color: #f0fdf4;">
                        <td style="padding: 10px;"><b>${idBahan}</b></td>
                        <td style="padding: 10px;">${namaBahan}</td>
                        <td style="padding: 10px; font-size:0.9rem; color:#555;">${deskripsiBahan}</td>
                        <td style="padding: 10px; text-align: right;">
                            <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteBahan('${idBahan}')">Hapus</button>
                        </td>
                    </tr>
                `;

                    list.insertAdjacentHTML('beforeend', newRow);

                } else {
                    const json = await res.json();
                    const msg = json.message || 'Cek inputan Anda';

                    if (res.status === 403) {
                        alert('⛔ AKSES DITOLAK: Hanya Admin yang boleh menambah bahan.');
                    } else {
                        alert('Gagal: ' + msg);
                    }
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan sistem');
            } finally {
                btnSimpan.innerText = textAsli;
                btnSimpan.disabled = false;
            }
        });

        async function deleteKategori(event, id) {
            event.stopPropagation();
            if(!confirm('Hapus Kategori? Pastikan kategori ini kosong!')) return;

            const res = await fetch(`/api/kategoris/${id}`, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });

            if(!res.ok) {
                const json = await res.json();
                alert(json.message);
                return;
            }

            if(currentKategoriId == id) {
                document.getElementById('emptyStateBahan').style.display = 'block';
                document.getElementById('bahanHeader').style.display = 'none';
                document.getElementById('tableBahan').style.display = 'none';
                currentKategoriId = null;
            }
            loadKategoris();
        }

        async function deleteBahan(id) {
            if(!confirm('Hapus Bahan ini?')) return;
            await fetch(`/api/bahans/${id}`, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });
            loadBahans(currentKategoriId);
        }

        function openModalKategori() {
            document.getElementById('formKategori').reset();
            document.getElementById('modalKategori').style.display = 'flex';
        }
        function closeModalKategori() { document.getElementById('modalKategori').style.display = 'none'; }

        function openModalBahan() {
            if (!currentKategoriId) {
                alert("⚠️ Eits! Silakan pilih salah satu Kategori di tabel kiri dulu.");
                return;
            }

            document.getElementById('formBahan').reset();
            document.getElementById('modalKategoriLabel').innerText = currentKategoriName;
            document.getElementById('modalBahan').style.display = 'flex';
        }
        function closeModalBahan() { document.getElementById('modalBahan').style.display = 'none'; }

        loadKategoris();

        window.onclick = function(event) {
            if (event.target == document.getElementById('modalKategori')) closeModalKategori();
            if (event.target == document.getElementById('modalBahan')) closeModalBahan();
        }
    </script>
@endpush

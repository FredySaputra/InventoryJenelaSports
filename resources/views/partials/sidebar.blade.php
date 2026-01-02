<nav class="sidebar">
    <div class="sidebar-header">Jelena Sports</div>
    <ul class="nav-links">
        <li>
            <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </li>

        <li>
            <a href="/kategori-bahan" class="{{ request()->is('kategori-bahan*') ? 'active' : '' }}">
                Kategori dan Bahan
            </a>
        </li>

        <li>
            <a href="/stok-barang" class="{{ request()->is('stok-barang') ? 'active' : '' }}">
                Stok Barang
            </a>
        </li>

        <li>
            <a href="/barang-keluar" class="{{ request()->is('barang-keluar') ? 'active' : '' }}">
                Barang Keluar
            </a>
        </li>

        <li>
            <a href="/karyawan" class="{{ request()->is('karyawan') ? 'active' : '' }}">
                Data Karyawan
            </a>
        </li>

        <li>
            <a href="/pelanggans" class="{{ request()->is('pelanggans*') ? 'active' : '' }}">
                Pelanggan
            </a>
        </li>
    </ul>
</nav>

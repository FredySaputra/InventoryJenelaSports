<div class="d-flex flex-column p-3">
    <div class="sidebar-brand mb-4 px-2 mt-2">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary text-white rounded p-1 d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                <i class="fas fa-dumbbell fa-lg"></i>
            </div>
            <h5 class="m-0 fw-bold text-white" style="letter-spacing: 0.5px;">Jelena Sports</h5>
        </div>
    </div>

    <ul class="nav nav-pills flex-column gap-1">

        <li class="nav-item">
            <a href="/dashboard" class="nav-link text-white {{ Request::is('dashboard') ? 'active bg-primary' : '' }}">
                <i class="fas fa-home fa-fw me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-header text-uppercase text-muted fw-bold mt-3 mb-2 px-3" style="font-size: 0.75rem;">Master Data</li>

        <li class="nav-item">
            <a href="/kategori-bahan" class="nav-link text-white {{ Request::is('kategori-bahan') ? 'active bg-primary' : '' }}">
                <i class="fas fa-tags fa-fw me-2"></i> Kategori & Bahan
            </a>
        </li>
        <li class="nav-item">
            <a href="/size" class="nav-link text-white {{ Request::is('size') ? 'active bg-primary' : '' }}">
                <i class="fas fa-ruler fa-fw me-2"></i> Ukuran (Size)
            </a>
        </li>
        <li class="nav-item">
            <a href="/produk" class="nav-link text-white {{ Request::is('produk') ? 'active bg-primary' : '' }}">
                <i class="fas fa-box fa-fw me-2"></i> Data Produk
            </a>
        </li>
        <li class="nav-item">
            <a href="/pelanggans" class="nav-link text-white {{ Request::is('pelanggans') ? 'active bg-primary' : '' }}">
                <i class="fas fa-users fa-fw me-2"></i> Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a href="/karyawan" class="nav-link text-white {{ Request::is('karyawan') ? 'active bg-primary' : '' }}">
                <i class="fas fa-id-card fa-fw me-2"></i> Data Karyawan
            </a>
        </li>

        <li class="nav-header text-uppercase text-muted fw-bold mt-3 mb-2 px-3" style="font-size: 0.75rem;">Transaksi</li>

        <li class="nav-item">
            <a href="/stok-barang" class="nav-link text-white {{ Request::is('stok-barang') ? 'active bg-primary' : '' }}">
                <i class="fas fa-cubes fa-fw me-2"></i> Stok Barang
            </a>
        </li>
        <li class="nav-item">
            <a href="/barang-keluar" class="nav-link text-white {{ Request::is('barang-keluar') ? 'active bg-primary' : '' }}">
                <i class="fas fa-shopping-cart fa-fw me-2"></i> Barang Keluar
            </a>
        </li>

    </ul>
</div>

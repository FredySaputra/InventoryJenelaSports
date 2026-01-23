<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jenela Sports Inventory')</title>

    <script>
        const tokenCheck = localStorage.getItem('api_token');
        if (!tokenCheck) window.location.href = '/login';
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --primary-color: #4f46e5;
            --bg-color: #f1f5f9;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Hilangkan scroll horizontal */
        }

        /* --- LAYOUT UTAMA --- */

        /* 1. Sidebar Tetap (Fixed) */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #1e293b; /* Warna Gelap */
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto; /* Scroll jika menu panjang */
            transition: all 0.3s;
        }

        /* 2. Area Konten (Sebelah Kanan) */
        .main-wrapper {
            margin-left: var(--sidebar-width); /* Geser konten ke kanan */
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        /* 3. Topbar (Sticky di atas) */
        .topbar {
            height: var(--topbar-height);
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
        }

        /* 4. Konten Halaman */
        .content-body {
            padding: 30px;
            flex-grow: 1;
        }

        /* --- AKSESORIS --- */
        .btn-logout {
            background-color: #ef4444; color: white; border: none;
            padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem;
            transition: 0.2s; display: flex; align-items: center; gap: 8px;
        }
        .btn-logout:hover { background-color: #dc2626; color: white; }

        /* Styling Judul Halaman di Topbar */
        .page-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; }

        /* User Profile di Topbar */
        .user-profile { text-align: right; line-height: 1.2; margin-right: 15px; }
        .user-profile small { color: #64748b; font-size: 0.75rem; display: block; }
        .user-profile span { color: #334155; font-size: 0.9rem; font-weight: 600; }
    </style>

    @stack('styles')
</head>
<body>

<nav class="sidebar">
    @include('partials.sidebar')
</nav>

<div class="main-wrapper">

    <header class="topbar">
        <h1 class="page-title">@yield('header-title', 'Dashboard')</h1>

        <div class="d-flex align-items-center">
            <div class="user-profile">
                <small>Halo, Admin</small>
                <span id="globalUserName">Administrator</span>
            </div>
            <button class="btn-logout" id="btnLogoutGlobal">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </header>

    <main class="content-body">
        @yield('content')
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Logic Logout & User Name
    const apiToken = localStorage.getItem('api_token');

    document.getElementById('btnLogoutGlobal').addEventListener('click', () => {
        if(confirm('Logout dari sistem?')) {
            localStorage.removeItem('api_token');
            window.location.href = '/login';
        }
    });

    // Tampilkan nama user (Optional)
    const storedName = localStorage.getItem('user_nama');
    if(storedName) document.getElementById('globalUserName').innerText = storedName;
</script>
@yield('modal-section')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

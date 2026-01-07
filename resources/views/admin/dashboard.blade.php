@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header-title', 'Overview Bisnis')

@push('styles')
    <style>
        /* 1. KARTU STATISTIK MODERN */
        .stat-card {
            border: none;
            border-radius: 15px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            background: white;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .stat-icon {
            position: absolute;
            right: 15px;
            top: 20px;
            font-size: 3rem;
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        .stat-label { font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: #1e293b; margin-top: 5px; }

        /* Warna Aksen Kartu */
        .border-l-primary { border-left: 5px solid #3b82f6; } /* Biru */
        .border-l-success { border-left: 5px solid #10b981; } /* Hijau */
        .border-l-warning { border-left: 5px solid #f59e0b; } /* Orange */
        .border-l-info    { border-left: 5px solid #6366f1; } /* Indigo */

        /* 2. CHART SECTION */
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
    </style>
@endpush

@section('content')

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card border-l-success">
                <div class="stat-label">Omset Hari Ini</div>
                <div class="stat-value text-success">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}</div>
                <i class="fas fa-wallet stat-icon text-success"></i>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card border-l-primary">
                <div class="stat-label">Transaksi Hari Ini</div>
                <div class="stat-value text-primary">{{ $trxHariIni }} <span class="fs-6 fw-normal text-muted">Trx</span></div>
                <i class="fas fa-shopping-cart stat-icon text-primary"></i>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card border-l-warning">
                <div class="stat-label">Total Produk</div>
                <div class="stat-value text-warning">{{ $totalProduk }} <span class="fs-6 fw-normal text-muted">Item</span></div>
                <i class="fas fa-box-open stat-icon text-warning"></i>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card border-l-info">
                <div class="stat-label">Total Karyawan</div>
                <div class="stat-value text-info">{{ $totalKaryawan }} <span class="fs-6 fw-normal text-muted">Orang</span></div>
                <i class="fas fa-users stat-icon text-info"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="chart-container h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Tren Penjualan (7 Hari Terakhir)</h5>
                    <span class="badge bg-light text-dark border">Realtime</span>
                </div>
                <canvas id="salesChart" style="max-height: 350px;"></canvas>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="chart-container h-100">
                <h5 class="fw-bold text-dark mb-3">Transaksi Terbaru</h5>

                <div class="list-group list-group-flush">
                    @forelse($transaksiTerbaru as $trx)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom-0 mb-2" style="background: #f8fafc; border-radius: 8px; padding: 10px;">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:0.9rem;">{{ $trx->id }}</div>
                                    <small class="text-muted">{{ $trx->pelanggan->nama ?? 'Umum' }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success" style="font-size:0.9rem;">+Rp {{ number_format($trx->totalTransaksi, 0, ',', '.') }}</div>
                                <small class="text-muted" style="font-size:0.7rem;">{{ \Carbon\Carbon::parse($trx->created_at)->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-store-slash fa-2x mb-2"></i><br>
                            Belum ada transaksi hari ini.
                        </div>
                    @endforelse
                </div>

                @if(count($transaksiTerbaru) > 0)
                    <div class="mt-3 text-center">
                        <a href="{{ url('/barang-keluar') }}" class="text-decoration-none small fw-bold">Lihat Semua Riwayat &rarr;</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Data dari Controller
        const labels = @json($chartLabels);
        const dataValues = @json($chartData);

        const ctx = document.getElementById('salesChart').getContext('2d');

        // Setup Gradient Warna untuk Grafik
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Biru transparan atas
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // Putih bawah

        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: dataValues,
                    borderColor: '#3b82f6', // Warna garis biru
                    backgroundColor: gradient, // Warna isi gradasi
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Membuat garis melengkung halus (curved)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }, // Sembunyikan legenda
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: {
                            callback: function(value, index, values) {
                                // Format sumbu Y jadi K (Ribuan) atau Jt (Juta) agar rapi
                                if(value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                                if(value >= 1000) return 'Rp ' + (value/1000) + ' Rb';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
@endpush

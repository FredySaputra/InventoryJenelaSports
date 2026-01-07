<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $omsetHariIni = Transaksi::whereDate('tanggalTransaksi', $today)->sum('totalTransaksi');
        $trxHariIni   = Transaksi::whereDate('tanggalTransaksi', $today)->count();
        $totalProduk  = Produk::count();
        $totalKaryawan = User::where('role', 'Karyawan')->count();

        $transaksiTerbaru = Transaksi::with('pelanggan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $chartLabels = [];
        $chartData   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');

            $total = Transaksi::whereDate('tanggalTransaksi', $date)->sum('totalTransaksi');
            $chartData[] = $total;
        }

        return view('admin.dashboard', compact(
            'omsetHariIni',
            'trxHariIni',
            'totalProduk',
            'totalKaryawan',
            'transaksiTerbaru',
            'chartLabels',
            'chartData'
        ));
    }
}

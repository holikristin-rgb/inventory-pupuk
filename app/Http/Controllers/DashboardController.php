<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\MutasiStok;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalJenis'    => Barang::count(),
            'totalStok'     => Barang::sum('stok'),
            'jumlahMenipis' => Barang::menipis()->count(),
            'masukHariIni'  => MutasiStok::where('jenis', 'masuk')->whereDate('created_at', today())->sum('jumlah'),
            'keluarHariIni' => MutasiStok::where('jenis', 'keluar')->whereDate('created_at', today())->sum('jumlah'),
            'barangMenipis' => Barang::menipis()->orderByRaw('CAST(stok AS SIGNED) - CAST(safety_stock AS SIGNED) ASC')->limit(8)->get(),
            'mutasiTerbaru' => MutasiStok::with(['barang', 'user'])->latest()->limit(8)->get(),
        ];

        return view('dashboard', $data);
    }
}

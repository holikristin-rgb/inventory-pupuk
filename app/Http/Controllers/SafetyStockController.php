<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class SafetyStockController extends Controller
{
    /** Rincian barang yang perlu pengadaan ulang (re-order). */
    public function index(Request $request)
    {
        $barang = Barang::menipis()
            ->orderByRaw('CAST(stok AS SIGNED) - CAST(safety_stock AS SIGNED) ASC')
            ->get();
        $notifikasi = $request->user()->unreadNotifications()->latest()->get();
        $sorot = (int) $request->query('barang');

        return view('monitoring.safety-stock', compact('barang', 'notifikasi', 'sorot'));
    }
}

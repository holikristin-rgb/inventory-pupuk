<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $barang = Barang::with(['lokasi' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->when($request->q, fn ($q, $kata) => $q->where(function ($w) use ($kata) {
                $w->where('nama', 'like', "%{$kata}%")
                  ->orWhere('kode_barang', 'like', "%{$kata}%");
            }))
            ->when($request->status === 'menipis', fn ($q) => $q->menipis()->where('stok', '>', 0))
            ->when($request->status === 'habis', fn ($q) => $q->where('stok', '<=', 0))
            ->when($request->status === 'aman', fn ($q) => $q->whereColumn('stok', '>', 'safety_stock'))
            ->orderBy('nama')
            ->get();

        return view('monitoring.index', compact('barang'));
    }
}

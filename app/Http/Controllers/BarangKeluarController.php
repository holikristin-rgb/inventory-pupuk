<?php

namespace App\Http\Controllers;

use App\Models\MutasiStok;
use App\Services\StokService;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $jenis  = 'keluar';
        $mutasi = MutasiStok::with(['barang', 'lokasi', 'user'])
            ->where('jenis', $jenis)
            ->latest()
            ->paginate(15);

        return view('mutasi.index', compact('mutasi', 'jenis'));
    }

    public function create()
    {
        return view('mutasi.keluar');
    }

    public function store(Request $request, StokService $stok)
    {
        $data = $request->validate([
            'barang_id'  => ['required', 'exists:barang,id'],
            'jumlah'     => ['required', 'integer', 'min:1'],
            'tujuan'     => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $mutasi = $stok->keluar(
            $data['barang_id'], $data['jumlah'],
            $data['tujuan'] ?? null, $data['keterangan'] ?? null
        );

        return redirect()->route('barang-keluar.index')
            ->with('sukses', "Transaksi berhasil. Sisa stok {$mutasi->barang->nama}: {$mutasi->stok_sesudah} {$mutasi->barang->satuan}.");
    }
}

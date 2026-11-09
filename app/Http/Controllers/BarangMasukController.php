<?php

namespace App\Http\Controllers;

use App\Models\LokasiRak;
use App\Models\MutasiStok;
use App\Services\StokService;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $jenis  = 'masuk';
        $mutasi = MutasiStok::with(['barang', 'lokasi', 'user'])
            ->where('jenis', $jenis)
            ->latest()
            ->paginate(15);

        return view('mutasi.index', compact('mutasi', 'jenis'));
    }

    public function create()
    {
        $lokasi = LokasiRak::orderBy('lorong')->orderBy('rak')->orderBy('tingkat')->orderBy('slot')->get();

        return view('mutasi.masuk', compact('lokasi'));
    }

    public function store(Request $request, StokService $stok)
    {
        $data = $request->validate([
            'barang_id'  => ['required', 'exists:barang,id'],
            'lokasi_id'  => ['required', 'exists:lokasi_rak,id'],
            'jumlah'     => ['required', 'integer', 'min:1'],
            'pemasok'    => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $mutasi = $stok->masuk(
            $data['barang_id'], $data['lokasi_id'], $data['jumlah'],
            $data['pemasok'] ?? null, $data['keterangan'] ?? null
        );

        return redirect()->route('barang-masuk.index')
            ->with('sukses', "Transaksi berhasil. Stok {$mutasi->barang->nama} sekarang {$mutasi->stok_sesudah} {$mutasi->barang->satuan}.");
    }
}

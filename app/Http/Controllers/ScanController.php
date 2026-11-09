<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan');
    }

    /**
     * Endpoint pencarian barang berdasarkan isi Barcode/QR Code.
     * Dipakai oleh halaman Pemindaian, Barang Masuk, Barang Keluar, dan Stock Opname.
     */
    public function cari(Request $request)
    {
        $kode = trim((string) $request->query('kode'));

        if ($kode === '') {
            return response()->json(['message' => 'Kode gagal terbaca. Silakan pindai ulang.'], 422);
        }

        $barang = Barang::with(['lokasi' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->where('kode_barang', $kode)
            ->first();

        if (! $barang) {
            return response()->json(['message' => 'Barang tidak terdaftar.'], 404);
        }

        return response()->json([
            'id'           => $barang->id,
            'kode_barang'  => $barang->kode_barang,
            'nama'         => $barang->nama,
            'satuan'       => $barang->satuan,
            'stok'         => $barang->stok,
            'safety_stock' => $barang->safety_stock,
            'status'       => $barang->status_label,
            'lokasi'       => $barang->lokasi->map(fn ($l) => [
                'label'  => $l->label,
                'jumlah' => $l->jumlah,
            ])->values(),
        ]);
    }
}

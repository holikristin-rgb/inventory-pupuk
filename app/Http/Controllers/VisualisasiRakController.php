<?php

namespace App\Http\Controllers;

use App\Models\LokasiRak;

class VisualisasiRakController extends Controller
{
    public function index()
    {
        $peta = LokasiRak::with('barang')
            ->orderBy('lorong')->orderBy('rak')->orderBy('tingkat')->orderBy('slot')
            ->get()
            ->groupBy('lorong')
            ->map(fn ($perLorong) => $perLorong->groupBy('rak')
                ->map(fn ($perRak) => $perRak->groupBy('tingkat')->sortKeysDesc()));

        return view('rak.index', compact('peta'));
    }

    /** Data pop-up rincian slot (dipanggil saat slot diklik). */
    public function slot(LokasiRak $lokasi)
    {
        $lokasi->load('barang');

        if ($lokasi->kosong) {
            return response()->json([
                'kosong' => true,
                'lokasi' => $lokasi->label,
                'pesan'  => 'Slot ini kosong.',
            ]);
        }

        return response()->json([
            'kosong'             => false,
            'lokasi'             => $lokasi->label,
            'jenis_pupuk'        => $lokasi->barang->nama,
            'volume_stok'        => $lokasi->jumlah . ' ' . $lokasi->barang->satuan,
            'kapasitas'          => $lokasi->kapasitas,
            'tanggal_penerimaan' => optional($lokasi->tanggal_penerimaan)->format('d M Y H:i'),
            'status'             => $lokasi->barang->status_label,
            'status_kode'        => $lokasi->barang->status,
        ]);
    }
}

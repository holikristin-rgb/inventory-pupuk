<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\LokasiRak;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LokasiRakController extends Controller
{
    public function index(Request $request)
    {
        $lokasi = LokasiRak::with('barang')
            ->when($request->lorong, fn ($q, $l) => $q->where('lorong', $l))
            ->orderBy('lorong')->orderBy('rak')->orderBy('tingkat')->orderBy('slot')
            ->paginate(20)
            ->withQueryString();

        $daftarLorong = LokasiRak::select('lorong')->distinct()->orderBy('lorong')->pluck('lorong');

        return view('lokasi.index', compact('lokasi', 'daftarLorong'));
    }

    /** Menambah slot secara massal pada satu lorong/rak/tingkat. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'lorong'      => ['required', 'string', 'max:10'],
            'rak'         => ['required', 'integer', 'min:1'],
            'tingkat'     => ['required', 'integer', 'min:1'],
            'jumlah_slot' => ['required', 'integer', 'min:1', 'max:50'],
            'kapasitas'   => ['required', 'integer', 'min:1'],
        ]);

        $lorong  = strtoupper($data['lorong']);
        $mulai   = (int) LokasiRak::where(['lorong' => $lorong, 'rak' => $data['rak'], 'tingkat' => $data['tingkat']])->max('slot');

        for ($i = 1; $i <= $data['jumlah_slot']; $i++) {
            LokasiRak::create([
                'lorong'    => $lorong,
                'rak'       => $data['rak'],
                'tingkat'   => $data['tingkat'],
                'slot'      => $mulai + $i,
                'kapasitas' => $data['kapasitas'],
            ]);
        }

        LogAktivitas::catat('tambah', 'lokasi_rak', "Menambah {$data['jumlah_slot']} slot pada Lorong {$lorong} Rak {$data['rak']} Tingkat {$data['tingkat']}");

        return back()->with('sukses', 'Slot penyimpanan berhasil ditambahkan.');
    }

    public function edit(LokasiRak $lokasi)
    {
        $barang = Barang::orderBy('nama')->get();

        return view('lokasi.edit', compact('lokasi', 'barang'));
    }

    /** Mengatur alokasi barang pada slot + kapasitas. */
    public function update(Request $request, LokasiRak $lokasi)
    {
        $data = $request->validate([
            'barang_id' => ['nullable', 'exists:barang,id'],
            'kapasitas' => ['required', 'integer', 'min:1'],
        ]);

        // Validasi ketersediaan slot: alokasi tidak boleh diganti bila slot masih berisi stok.
        if ($lokasi->jumlah > 0 && (int) $data['barang_id'] !== (int) $lokasi->barang_id) {
            throw ValidationException::withMessages([
                'barang_id' => 'Slot masih berisi stok, alokasi barang tidak dapat diubah.',
            ]);
        }
        if ($data['kapasitas'] < $lokasi->jumlah) {
            throw ValidationException::withMessages([
                'kapasitas' => "Kapasitas tidak boleh lebih kecil dari isi slot saat ini ({$lokasi->jumlah}).",
            ]);
        }

        $lokasi->update([
            'barang_id' => $data['barang_id'] ?: null,
            'kapasitas' => $data['kapasitas'],
        ]);

        LogAktivitas::catat('ubah', 'lokasi_rak', "Mengatur alokasi {$lokasi->label}");

        return redirect()->route('lokasi.index')->with('sukses', 'Pemetaan lokasi berhasil disimpan.');
    }

    public function destroy(LokasiRak $lokasi)
    {
        if ($lokasi->jumlah > 0) {
            return back()->with('error', 'Slot masih berisi stok, tidak dapat dihapus.');
        }

        $label = $lokasi->label;
        $lokasi->delete();

        LogAktivitas::catat('hapus', 'lokasi_rak', "Menghapus slot {$label}");

        return back()->with('sukses', 'Slot berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barang = Barang::query()
            ->when($request->q, fn ($q, $kata) => $q->where(function ($w) use ($kata) {
                $w->where('nama', 'like', "%{$kata}%")
                  ->orWhere('kode_barang', 'like', "%{$kata}%")
                  ->orWhere('jenis', 'like', "%{$kata}%");
            }))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.form', ['barang' => new Barang(['satuan' => 'karung'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['kode_barang'] = $data['kode_barang'] ?: 'BRG-' . strtoupper(Str::random(6));

        $barang = Barang::create($data);

        LogAktivitas::catat('tambah', 'barang', "Menambah barang {$barang->nama} ({$barang->kode_barang})");

        return redirect()->route('barang.index')->with('sukses', 'Data barang berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load(['lokasi' => fn ($q) => $q->where('jumlah', '>', 0)]);
        $mutasi = $barang->mutasi()->with('user')->latest()->limit(10)->get();

        return view('barang.show', compact('barang', 'mutasi'));
    }

    public function edit(Barang $barang)
    {
        return view('barang.form', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $this->validasi($request, $barang);
        $data['kode_barang'] = $data['kode_barang'] ?: $barang->kode_barang;

        $barang->update($data);

        LogAktivitas::catat('ubah', 'barang', "Mengubah data barang {$barang->nama} ({$barang->kode_barang})");

        return redirect()->route('barang.index')->with('sukses', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->stok > 0 || $barang->mutasi()->exists() || $barang->opnameDetail()->exists()) {
            return back()->with('error', 'Barang tidak dapat dihapus karena masih memiliki stok atau riwayat transaksi.');
        }

        $nama = $barang->nama;
        $barang->delete();

        LogAktivitas::catat('hapus', 'barang', "Menghapus barang {$nama}");

        return redirect()->route('barang.index')->with('sukses', 'Data barang berhasil dihapus.');
    }

    private function validasi(Request $request, ?Barang $barang = null): array
    {
        return $request->validate([
            'kode_barang'         => ['nullable', 'string', 'max:100', Rule::unique('barang', 'kode_barang')->ignore($barang?->id)],
            'nama'                => ['required', 'string', 'max:150'],
            'jenis'               => ['nullable', 'string', 'max:100'],
            'satuan'              => ['required', 'string', 'max:20'],
            'pemakaian_rata_rata' => ['required', 'numeric', 'min:0'],
            'pemakaian_maksimum'  => ['required', 'numeric', 'gte:pemakaian_rata_rata'],
            'lead_time'           => ['required', 'integer', 'min:0'],
            'keterangan'          => ['nullable', 'string'],
        ], [
            'pemakaian_maksimum.gte' => 'Pemakaian maksimum tidak boleh lebih kecil dari pemakaian rata-rata.',
        ]);
    }
}

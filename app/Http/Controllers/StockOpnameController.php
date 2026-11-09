<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opname = StockOpname::with('user')->withCount([
            'detail',
            'detail as selisih_count' => fn ($q) => $q->where('selisih', '!=', 0),
        ])->latest()->paginate(15);

        return view('opname.index', compact('opname'));
    }

    public function create()
    {
        return view('opname.create');
    }

    /** Finalisasi opname: simpan dokumen hasil stock opname. */
    public function store(Request $request)
    {
        $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.barang_id'  => ['required', 'exists:barang,id', 'distinct'],
            'items.*.stok_fisik' => ['required', 'integer', 'min:0'],
            'items.*.keterangan' => ['nullable', 'string'],
            'catatan'            => ['nullable', 'string'],
        ], [
            'items.required' => 'Pindai minimal satu barang sebelum finalisasi.',
        ]);

        $opname = DB::transaction(function () use ($request) {
            $opname = StockOpname::create([
                'kode'    => 'OPN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => $request->user()->id,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $i => $item) {
                $barang  = Barang::findOrFail($item['barang_id']);
                $selisih = (int) $item['stok_fisik'] - $barang->stok;

                // Bila ada selisih, keterangan wajib dilengkapi.
                if ($selisih !== 0 && blank($item['keterangan'] ?? null)) {
                    throw ValidationException::withMessages([
                        "items.{$i}.keterangan" => "Keterangan selisih {$barang->nama} wajib diisi.",
                    ]);
                }

                $opname->detail()->create([
                    'barang_id'   => $barang->id,
                    'stok_sistem' => $barang->stok,
                    'stok_fisik'  => (int) $item['stok_fisik'],
                    'selisih'     => $selisih,
                    'keterangan'  => $item['keterangan'] ?? null,
                ]);
            }

            return $opname;
        });

        $jumlahSelisih = $opname->detail()->where('selisih', '!=', 0)->count();
        $total         = $opname->detail()->count();

        LogAktivitas::catat('opname', 'stock_opname', "Finalisasi stock opname {$opname->kode}: {$total} barang diperiksa, {$jumlahSelisih} selisih");

        return redirect()->route('opname.show', $opname)
            ->with('sukses', "Stock opname selesai: {$total} barang diperiksa, {$jumlahSelisih} barang memiliki selisih.");
    }

    public function show(StockOpname $opname)
    {
        $opname->load(['user', 'detail.barang']);

        return view('opname.show', compact('opname'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Barang;
use App\Models\MutasiStok;
use App\Models\StockOpnameDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $laporan = null;

        if ($request->filled('jenis')) {
            $request->validate([
                'jenis'        => ['required', 'in:persediaan,mutasi,stock_opname'],
                'dari'         => ['nullable', 'date'],
                'sampai'       => ['nullable', 'date', 'after_or_equal:dari'],
                'jenis_mutasi' => ['nullable', 'in:semua,masuk,keluar'],
            ]);

            $laporan = $this->susun($request);

            if (count($laporan['baris']) === 0) {
                return redirect()->route('laporan.index')
                    ->withInput()
                    ->with('error', 'Data laporan tidak ditemukan untuk filter yang dipilih.');
            }
        }

        return view('laporan.index', compact('laporan'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'jenis'  => ['required', 'in:persediaan,mutasi,stock_opname'],
            'format' => ['required', 'in:pdf,excel'],
        ]);

        $laporan = $this->susun($request);
        $nama    = 'laporan-' . str_replace('_', '-', $request->jenis) . '-' . now()->format('Ymd-His');

        if ($request->format === 'pdf') {
            return Pdf::loadView('laporan.pdf', $laporan)
                ->setPaper('a4', 'landscape')
                ->download($nama . '.pdf');
        }

        return Excel::download(new LaporanExport($laporan['kolom'], $laporan['baris']), $nama . '.xlsx');
    }

    /** Mengambil data laporan sesuai jenis, periode, dan jenis mutasi. */
    private function susun(Request $request): array
    {
        $dari   = $request->input('dari') ?: now()->startOfMonth()->toDateString();
        $sampai = $request->input('sampai') ?: now()->toDateString();

        return match ($request->input('jenis')) {
            'mutasi'       => $this->laporanMutasi($dari, $sampai, $request->input('jenis_mutasi', 'semua')),
            'stock_opname' => $this->laporanOpname($dari, $sampai),
            default        => $this->laporanPersediaan(),
        };
    }

    private function laporanPersediaan(): array
    {
        $baris = Barang::with(['lokasi' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->orderBy('nama')->get()
            ->map(fn ($b) => [
                $b->kode_barang, $b->nama, $b->jenis, $b->stok, $b->satuan, $b->safety_stock,
                $b->status_label,
                $b->lokasi->map(fn ($l) => $l->kode . ' (' . $l->jumlah . ')')->implode(', ') ?: '-',
            ])->all();

        return [
            'judul'   => 'Laporan Persediaan Barang',
            'periode' => 'Per ' . now()->format('d M Y H:i'),
            'kolom'   => ['Kode', 'Nama Barang', 'Jenis', 'Stok', 'Satuan', 'Safety Stock', 'Status', 'Lokasi Rak'],
            'baris'   => $baris,
            'jenis'   => 'persediaan',
        ];
    }

    private function laporanMutasi(string $dari, string $sampai, string $jenisMutasi): array
    {
        $baris = MutasiStok::with(['barang', 'user'])
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->when($jenisMutasi !== 'semua', fn ($q) => $q->where('jenis', $jenisMutasi))
            ->orderBy('created_at')->get()
            ->map(fn ($m) => [
                $m->created_at->format('d/m/Y H:i'), $m->kode_transaksi, $m->barang->nama,
                strtoupper($m->jenis), $m->jumlah, $m->stok_sebelum, $m->stok_sesudah,
                $m->user->name, $m->pihak ?: '-',
            ])->all();

        return [
            'judul'   => 'Laporan Mutasi Barang ' . ($jenisMutasi === 'semua' ? 'Masuk & Keluar' : ucfirst($jenisMutasi)),
            'periode' => "Periode {$dari} s/d {$sampai}",
            'kolom'   => ['Tanggal', 'Kode Transaksi', 'Barang', 'Jenis', 'Jumlah', 'Stok Sebelum', 'Stok Sesudah', 'Petugas', 'Pemasok/Tujuan'],
            'baris'   => $baris,
            'jenis'   => 'mutasi',
        ];
    }

    private function laporanOpname(string $dari, string $sampai): array
    {
        $baris = StockOpnameDetail::with(['opname.user', 'barang'])
            ->whereHas('opname', fn ($q) => $q->whereDate('created_at', '>=', $dari)->whereDate('created_at', '<=', $sampai))
            ->orderBy('stock_opname_id')->get()
            ->map(fn ($d) => [
                $d->opname->created_at->format('d/m/Y H:i'), $d->opname->kode, $d->opname->user->name,
                $d->barang->nama, $d->stok_sistem, $d->stok_fisik, $d->selisih, $d->keterangan ?: '-',
            ])->all();

        return [
            'judul'   => 'Laporan Hasil Stock Opname',
            'periode' => "Periode {$dari} s/d {$sampai}",
            'kolom'   => ['Tanggal', 'Kode Opname', 'Petugas', 'Barang', 'Stok Sistem', 'Stok Fisik', 'Selisih', 'Keterangan'],
            'baris'   => $baris,
            'jenis'   => 'stock_opname',
        ];
    }
}

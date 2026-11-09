<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\LokasiRak;
use App\Models\MutasiStok;
use App\Models\User;
use App\Notifications\SafetyStockNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Seluruh perubahan stok (barang masuk & keluar) lewat service ini agar
 * stok, peta rak, riwayat mutasi, log aktivitas, dan peringatan Safety Stock
 * selalu diperbarui bersamaan dalam satu transaksi database.
 */
class StokService
{
    public function masuk(
        int $barangId,
        int $lokasiId,
        int $jumlah,
        ?string $pemasok = null,
        ?string $keterangan = null
    ): MutasiStok {
        $mutasi = DB::transaction(function () use ($barangId, $lokasiId, $jumlah, $pemasok, $keterangan) {
            $barang = Barang::lockForUpdate()->findOrFail($barangId);
            $lokasi = LokasiRak::lockForUpdate()->findOrFail($lokasiId);

            if ($lokasi->jumlah > 0 && (int) $lokasi->barang_id !== (int) $barang->id) {
                throw ValidationException::withMessages([
                    'lokasi_id' => "Slot {$lokasi->label} sudah berisi barang lain.",
                ]);
            }

            $sisaRuang = $lokasi->kapasitas - $lokasi->jumlah;
            if ($jumlah > $sisaRuang) {
                throw ValidationException::withMessages([
                    'jumlah' => "Melebihi kapasitas slot. Sisa ruang pada slot ini: {$sisaRuang}.",
                ]);
            }

            $stokSebelum = $barang->stok;

            // 1) tambah stok + 2) perbarui peta rak
            $lokasi->barang_id = $barang->id;
            $lokasi->jumlah += $jumlah;
            $lokasi->tanggal_penerimaan = now();
            $lokasi->save();

            $barang->stok += $jumlah;
            $barang->save();

            // 3) catat riwayat mutasi masuk
            $mutasi = MutasiStok::create([
                'kode_transaksi' => $this->kode('MSK'),
                'barang_id'      => $barang->id,
                'lokasi_id'      => $lokasi->id,
                'user_id'        => Auth::id(),
                'jenis'          => 'masuk',
                'jumlah'         => $jumlah,
                'stok_sebelum'   => $stokSebelum,
                'stok_sesudah'   => $barang->stok,
                'pihak'          => $pemasok,
                'keterangan'     => $keterangan,
            ]);

            LogAktivitas::catat(
                'masuk',
                'barang_masuk',
                "Barang masuk {$barang->nama} sebanyak {$jumlah} {$barang->satuan} ke {$lokasi->label} ({$mutasi->kode_transaksi})"
            );

            $this->periksaSafetyStock($barang);

            return $mutasi;
        });

        return $mutasi->load('barang', 'lokasi');
    }

    public function keluar(
        int $barangId,
        int $jumlah,
        ?string $tujuan = null,
        ?string $keterangan = null
    ): MutasiStok {
        $mutasi = DB::transaction(function () use ($barangId, $jumlah, $tujuan, $keterangan) {
            $barang = Barang::lockForUpdate()->findOrFail($barangId);

            // Cek ketersediaan stok; bila kurang, transaksi dibatalkan.
            if ($jumlah > $barang->stok) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok tidak mencukupi. Sisa stok {$barang->nama}: {$barang->stok} {$barang->satuan}.",
                ]);
            }

            // Ambil dari slot terlama dahulu (FIFO).
            $slots = LokasiRak::where('barang_id', $barang->id)
                ->where('jumlah', '>', 0)
                ->orderBy('tanggal_penerimaan')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $sisa = $jumlah;
            $rincian = [];

            foreach ($slots as $slot) {
                if ($sisa <= 0) {
                    break;
                }
                $ambil = min($slot->jumlah, $sisa);
                $slot->jumlah -= $ambil;
                $slot->save();

                $sisa -= $ambil;
                $rincian[] = ['lokasi_id' => $slot->id, 'lokasi' => $slot->label, 'jumlah' => $ambil];
            }

            if ($sisa > 0) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok pada rak tidak sinkron dengan stok sistem. Lakukan stock opname atau periksa pemetaan lokasi.',
                ]);
            }

            $stokSebelum = $barang->stok;
            $barang->stok -= $jumlah;
            $barang->save();

            $mutasi = MutasiStok::create([
                'kode_transaksi' => $this->kode('KLR'),
                'barang_id'      => $barang->id,
                'lokasi_id'      => null,
                'user_id'        => Auth::id(),
                'jenis'          => 'keluar',
                'jumlah'         => $jumlah,
                'stok_sebelum'   => $stokSebelum,
                'stok_sesudah'   => $barang->stok,
                'pihak'          => $tujuan,
                'keterangan'     => $keterangan,
                'rincian_lokasi' => $rincian,
            ]);

            LogAktivitas::catat(
                'keluar',
                'barang_keluar',
                "Barang keluar {$barang->nama} sebanyak {$jumlah} {$barang->satuan} ({$mutasi->kode_transaksi})"
            );

            $this->periksaSafetyStock($barang);

            return $mutasi;
        });

        return $mutasi->load('barang');
    }

    /**
     * Kirim notifikasi peringatan dini ke Kepala Gudang bila stok <= Safety Stock.
     * Notifikasi yang belum dibaca otomatis ditandai selesai saat stok kembali aman.
     */
    public function periksaSafetyStock(Barang $barang): void
    {
        $menipis = $barang->stok <= $barang->safety_stock;

        foreach (User::where('role', User::KEPALA)->get() as $kepala) {
            $ada = $kepala->unreadNotifications
                ->first(fn ($n) => ($n->data['barang_id'] ?? null) == $barang->id);

            if ($menipis && ! $ada) {
                $kepala->notify(new SafetyStockNotification($barang));
            } elseif (! $menipis && $ada) {
                $ada->markAsRead();
            }
        }
    }

    private function kode(string $prefix): string
    {
        return $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
    }
}

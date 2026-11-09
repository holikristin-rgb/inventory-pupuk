<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\LokasiRak;
use App\Models\LogAktivitas;
use App\Models\MutasiStok;
use App\Models\StockOpname;
use App\Models\User;
use App\Services\StokService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * 1. MENYIAPKAN AKUN
         */

        $kepala = User::firstOrCreate(
            ['username' => 'kepala'],
            [
                'name' => 'Kepala Gudang',
                'email' => 'kepala@example.com',
                'password' => 'password',
                'role' => User::KEPALA,
            ]
        );

        foreach (config('akses.staf', []) as $username => $akun) {
            User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => $akun['name'],
                    'email' => $username . '@example.com',
                    'password' => $akun['password'],
                    'role' => User::STAF,
                ]
            );
        }

        $auditor = User::firstOrCreate(
            ['username' => 'auditor'],
            [
                'name' => 'Auditor',
                'email' => 'auditor@example.com',
                'password' => 'password',
                'role' => User::AUDITOR,
            ]
        );

        $staf = User::where('role', User::STAF)->first();

        /*
         * 2. DATA CONTOH BARANG
         *
         * Format:
         * kode, nama, jenis, rata-rata, maksimum, lead time, stok akhir
         */

        $daftarPupuk = [
            ['PPK-0001', 'Pupuk Urea 50 Kg', 'Urea', 10, 16, 5, 220],
            ['PPK-0002', 'Pupuk NPK 50 Kg', 'NPK', 8, 14, 7, 30],
            ['PPK-0003', 'Pupuk ZA 50 Kg', 'ZA', 5, 9, 4, 90],
            ['PPK-0004', 'Pupuk SP-36 50 Kg', 'SP-36', 6, 10, 6, 20],
            ['PPK-0005', 'Pupuk KCl 50 Kg', 'KCl', 4, 7, 5, 0],
            ['PPK-0006', 'Pupuk Organik 40 Kg', 'Organik', 3, 5, 4, 60],
            ['PPK-0007', 'Pupuk TSP 50 Kg', 'TSP', 4, 7, 5, 12],
            ['PPK-0008', 'Pupuk Dolomit 50 Kg', 'Dolomit', 6, 9, 3, 45],
            ['PPK-0009', 'Pupuk Kieserite 50 Kg', 'Kieserite', 2, 4, 5, 8],
            ['PPK-0010', 'Pupuk NPK Phonska 50 Kg', 'Phonska', 7, 11, 6, 120],
        ];

        $barangDemo = [];

        foreach ($daftarPupuk as $data) {
            [$kode, $nama, $jenis, $rata, $maks, $lead] = $data;

            $barangDemo[$kode] = Barang::firstOrCreate(
                ['kode_barang' => $kode],
                [
                    'nama' => $nama,
                    'jenis' => $jenis,
                    'satuan' => 'karung',
                    'stok' => 0,
                    'pemakaian_rata_rata' => $rata,
                    'pemakaian_maksimum' => $maks,
                    'lead_time' => $lead,
                    'keterangan' => 'Data contoh untuk demonstrasi sistem inventory.',
                ]
            );
        }

        /*
         * 3. MEMBUAT 48 SLOT RAK
         */

        foreach (['A', 'B'] as $lorong) {
            foreach ([1, 2] as $rak) {
                foreach ([1, 2, 3] as $tingkat) {
                    foreach ([1, 2, 3, 4] as $slot) {
                        LokasiRak::firstOrCreate(
                            compact('lorong', 'rak', 'tingkat', 'slot'),
                            ['kapasitas' => 100]
                        );
                    }
                }
            }
        }

        /*
         * 4. TRANSAKSI BARANG MASUK DAN KELUAR
         *
         * Hanya dibuat jika belum ada transaksi.
         * Semua perubahan stok menggunakan StokService.
         */

        if (! MutasiStok::exists()) {
            if (! $staf) {
                throw new \RuntimeException(
                    'Akun staf belum tersedia. Periksa config/akses.php.'
                );
            }

            $stokService = app(StokService::class);

            Auth::login($staf);

            DB::transaction(function () use (
                $daftarPupuk,
                $barangDemo,
                $stokService
            ) {
                $slotId = 0;

                foreach ($daftarPupuk as $data) {
                    [$kode, $nama, $jenis, $rata, $maks, $lead, $stokAkhir] = $data;

                    $barang = $barangDemo[$kode];

                    // Stok masuk awal dibuat lebih besar dari stok akhir.
                    $stokKeluar = match ($kode) {
                        'PPK-0001' => 80,
                        'PPK-0002' => 20,
                        'PPK-0003' => 30,
                        'PPK-0004' => 15,
                        'PPK-0005' => 20,
                        'PPK-0006' => 20,
                        'PPK-0007' => 10,
                        'PPK-0008' => 15,
                        'PPK-0009' => 12,
                        'PPK-0010' => 40,
                        default => 0,
                    };

                    $stokAwal = $stokAkhir + $stokKeluar;

                    // Isi slot dengan transaksi masuk, maksimal 100 karung per slot.
                    $sisa = $stokAwal;

                    while ($sisa > 0) {
                        $jumlah = min(100, $sisa);

                        $slot = LokasiRak::orderBy('id')
                            ->skip($slotId)
                            ->first();

                        if (! $slot) {
                            throw new \RuntimeException(
                                'Slot rak tidak mencukupi untuk data contoh.'
                            );
                        }

                        $stokService->masuk(
                            $barang->id,
                            $slot->id,
                            $jumlah,
                            'PT Pemasok Pupuk Sejahtera',
                            'Penerimaan stok awal data demonstrasi.'
                        );

                        $sisa -= $jumlah;
                        $slotId++;
                    }

                    // Transaksi barang keluar.
                    if ($stokKeluar > 0) {
                        $stokService->keluar(
                            $barang->id,
                            $stokKeluar,
                            'Distribusi Gudang',
                            'Pengeluaran stok untuk demonstrasi.'
                        );
                    }
                }
            });

            Auth::logout();
        }

        /*
         * 5. DATA STOCK OPNAME
         *
         * Mencatat beberapa barang dengan dan tanpa selisih.
         * Tidak mengubah stok aktual barang.
         */

        if (! StockOpname::where('kode', 'OPN-DEMO-2026')->exists()) {
            $opname = StockOpname::create([
                'kode' => 'OPN-DEMO-2026',
                'user_id' => $auditor->id,
            ]);

            $selisihDemo = [
                'PPK-0001' => [0, null],
                'PPK-0002' => [-2, 'Ditemukan selisih akibat kerusakan kemasan.'],
                'PPK-0003' => [0, null],
                'PPK-0004' => [3, 'Terdapat stok fisik yang belum tercatat.'],
                'PPK-0005' => [0, null],
                'PPK-0006' => [-1, 'Perbedaan hasil perhitungan fisik.'],
            ];

            foreach ($selisihDemo as $kode => [$selisih, $keterangan]) {
                $barang = $barangDemo[$kode];

                $opname->detail()->create([
                    'barang_id' => $barang->id,
                    'stok_sistem' => $barang->stok,
                    'stok_fisik' => max(0, $barang->stok + $selisih),
                    'selisih' => $selisih,
                    'keterangan' => $keterangan,
                ]);
            }

            LogAktivitas::catat(
                'opname',
                'stock_opname',
                'Pembuatan data stock opname demonstrasi.',
                $auditor->id
            );
        }

        /*
         * 6. LOG AKTIVITAS TAMBAHAN
         */

        $logDemo = [
            ['tambah', 'barang', 'Penambahan data barang contoh.'],
            ['tambah', 'lokasi_rak', 'Pembuatan slot lokasi penyimpanan contoh.'],
            ['login', 'auth', 'Pengguna masuk ke sistem inventory.'],
            ['ubah', 'barang', 'Pembaruan informasi data barang.'],
        ];

        foreach ($logDemo as [$aksi, $modul, $deskripsi]) {
            LogAktivitas::firstOrCreate(
                ['deskripsi' => $deskripsi],
                [
                    'user_id' => $kepala->id,
                    'aksi' => $aksi,
                    'modul' => $modul,
                    'ip_address' => '127.0.0.1',
                ]
            );
        }

        $this->command->info('Data contoh inventory pupuk berhasil disiapkan.');
    }
}
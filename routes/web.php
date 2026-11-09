<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\LokasiRakController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\SafetyStockController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\VisualisasiRakController;
use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

$kepala  = User::KEPALA;
$staf    = User::STAF;
$auditor = User::AUDITOR;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

/* ---------- Login ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.proses');

    // Registrasi hanya untuk Kepala Gudang & Auditor (staf tidak bisa mendaftar)
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.proses');
});

Route::middleware('auth')->group(function () use ($kepala, $staf, $auditor) {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ---------- Staf Gudang saja ---------- */
    Route::middleware(CheckRole::class . ':' . $staf)->group(function () {
        // PENTING: didaftarkan sebelum route show agar /barang/create tidak terbaca sebagai {barang}
        Route::resource('barang', BarangController::class)->except(['index', 'show']);

        Route::get('/barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
        Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
        Route::get('/barang-keluar/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
        Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');

        Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
        Route::get('/scan/cari', [ScanController::class, 'cari'])->name('scan.cari');

        Route::get('/stock-opname/create', [StockOpnameController::class, 'create'])->name('opname.create');
        Route::post('/stock-opname', [StockOpnameController::class, 'store'])->name('opname.store');
    });

    /* ---------- Kelola lokasi: Staf Gudang / Kepala Gudang ---------- */
    Route::middleware(CheckRole::class . ':' . $staf . ',' . $kepala)->group(function () {
        Route::resource('lokasi', LokasiRakController::class)->except(['index', 'show', 'create']);
        Route::get('/lokasi', [LokasiRakController::class, 'index'])->name('lokasi.index');
    });

    /* ---------- Semua peran (Auditor read-only) ---------- */
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');

    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');

    Route::get('/rak', [VisualisasiRakController::class, 'index'])->name('rak.index');
    Route::get('/rak/slot/{lokasi}', [VisualisasiRakController::class, 'slot'])->name('rak.slot');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('opname.index');
    Route::get('/stock-opname/{opname}', [StockOpnameController::class, 'show'])->name('opname.show');

    /* ---------- Kepala Gudang ---------- */
    Route::get('/safety-stock', [SafetyStockController::class, 'index'])
        ->middleware(CheckRole::class . ':' . $kepala)
        ->name('safety-stock.index');

    /* ---------- Kepala Gudang / Auditor ---------- */
    Route::middleware(CheckRole::class . ':' . $kepala . ',' . $auditor)->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        Route::get('/riwayat-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');
    });
});

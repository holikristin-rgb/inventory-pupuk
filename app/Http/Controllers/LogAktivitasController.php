<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /** Riwayat aktivitas bersifat read-only. */
    public function index(Request $request)
    {
        $log = LogAktivitas::with('user')
            ->when($request->dari, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->sampai, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->when($request->aksi, fn ($q, $v) => $q->where('aksi', $v))
            ->latest('created_at')->latest('id')
            ->paginate(25)
            ->withQueryString();

        $pengguna = User::orderBy('name')->get();
        $daftarAksi = LogAktivitas::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi');

        return view('log.index', compact('log', 'pengguna', 'daftarAksi'));
    }
}

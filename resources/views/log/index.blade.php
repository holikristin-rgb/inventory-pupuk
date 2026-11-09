@extends('layouts.app')
@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="card">
    <form method="GET" class="mb-4 grid items-end gap-3 sm:grid-cols-5">
        <div><label class="label">Dari</label><input type="date" name="dari" value="{{ request('dari') }}" class="input"></div>
        <div><label class="label">Sampai</label><input type="date" name="sampai" value="{{ request('sampai') }}" class="input"></div>
        <div>
            <label class="label">Pengguna</label>
            <select name="user_id" class="input">
                <option value="">Semua</option>
                @foreach ($pengguna as $p)
                    <option value="{{ $p->id }}" @selected(request('user_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Jenis Aktivitas</label>
            <select name="aksi" class="input">
                <option value="">Semua</option>
                @foreach ($daftarAksi as $a)
                    <option value="{{ $a }}" @selected(request('aksi') === $a)>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2"><button class="btn-primary">Filter</button><a href="{{ route('log.index') }}" class="btn-secondary">Reset</a></div>
    </form>

    <div class="overflow-x-auto">
    <table class="w-full">
        <thead><tr><th class="th">Waktu</th><th class="th">Pengguna</th><th class="th">Aktivitas</th><th class="th">Modul</th><th class="th">Deskripsi</th><th class="th">IP</th></tr></thead>
        <tbody>
        @forelse ($log as $l)
            <tr>
                <td class="td whitespace-nowrap">{{ $l->created_at->format('d/m/Y H:i:s') }}</td>
                <td class="td">{{ $l->user?->name ?? '-' }}</td>
                <td class="td"><span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold">{{ strtoupper($l->aksi) }}</span></td>
                <td class="td">{{ $l->modul }}</td>
                <td class="td">{{ $l->deskripsi }}</td>
                <td class="td text-xs text-slate-500">{{ $l->ip_address }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="td text-center text-slate-400">Data tidak ditemukan.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $log->links() }}</div>
</div>
@endsection

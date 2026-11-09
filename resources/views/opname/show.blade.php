@extends('layouts.app')
@section('title', 'Hasil Stock Opname')

@section('content')
<div class="card">
    <div class="mb-4 grid gap-2 text-sm sm:grid-cols-3">
        <div><span class="text-slate-500">Kode</span><div class="font-mono font-semibold">{{ $opname->kode }}</div></div>
        <div><span class="text-slate-500">Petugas</span><div class="font-semibold">{{ $opname->user->name }}</div></div>
        <div><span class="text-slate-500">Tanggal</span><div class="font-semibold">{{ $opname->created_at->format('d/m/Y H:i') }}</div></div>
    </div>
    @if ($opname->catatan)
        <p class="mb-4 rounded bg-slate-50 p-3 text-sm">{{ $opname->catatan }}</p>
    @endif

    <table class="w-full">
        <thead><tr><th class="th">Barang</th><th class="th">Stok Sistem</th><th class="th">Stok Fisik</th><th class="th">Selisih</th><th class="th">Keterangan</th></tr></thead>
        <tbody>
        @foreach ($opname->detail as $d)
            <tr class="{{ $d->selisih != 0 ? 'bg-red-50' : '' }}">
                <td class="td">{{ $d->barang->nama }}</td>
                <td class="td">{{ $d->stok_sistem }}</td>
                <td class="td">{{ $d->stok_fisik }}</td>
                <td class="td font-semibold {{ $d->selisih == 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $d->selisih > 0 ? '+' : '' }}{{ $d->selisih }}</td>
                <td class="td">{{ $d->keterangan ?: '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <a href="{{ route('opname.index') }}" class="btn-secondary mt-4">&larr; Kembali</a>
</div>
@endsection

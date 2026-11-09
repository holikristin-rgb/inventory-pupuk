@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php($u = auth()->user())

@if ($jumlahMenipis > 0)
    <div class="mb-5 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <div><span class="font-semibold">Peringatan Safety Stock:</span> {{ $jumlahMenipis }} barang berada pada atau di bawah batas Safety Stock.</div>
        @if ($u->hasRole('kepala_gudang'))
            <a href="{{ route('safety-stock.index') }}" class="btn-danger">Lihat Rincian</a>
        @else
            <a href="{{ route('monitoring.index', ['status' => 'menipis']) }}" class="btn-danger">Lihat Barang</a>
        @endif
    </div>
@endif

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="card"><div class="text-sm text-slate-500">Jenis Barang</div><div class="text-3xl font-bold">{{ number_format($totalJenis) }}</div></div>
    <div class="card"><div class="text-sm text-slate-500">Total Stok</div><div class="text-3xl font-bold">{{ number_format($totalStok) }}</div></div>
    <div class="card"><div class="text-sm text-slate-500">Masuk / Keluar Hari Ini</div><div class="text-3xl font-bold"><span class="text-emerald-600">+{{ $masukHariIni }}</span> / <span class="text-red-600">-{{ $keluarHariIni }}</span></div></div>
    <div class="card"><div class="text-sm text-slate-500">Di Bawah Safety Stock</div><div class="text-3xl font-bold {{ $jumlahMenipis ? 'text-red-600' : 'text-emerald-600' }}">{{ $jumlahMenipis }}</div></div>
</div>

@if ($u->hasRole('staf_gudang'))
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('barang-masuk.create') }}" class="btn-primary">+ Barang Masuk</a>
        <a href="{{ route('barang-keluar.create') }}" class="btn-primary">- Barang Keluar</a>
        <a href="{{ route('scan.index') }}" class="btn-secondary">Pindai Barcode/QR</a>
        <a href="{{ route('opname.create') }}" class="btn-secondary">Stock Opname</a>
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-2">
    <div class="card">
        <h2 class="mb-3 font-semibold">Barang Perlu Perhatian</h2>
        <table class="w-full">
            <thead><tr><th class="th">Barang</th><th class="th">Stok</th><th class="th">Safety Stock</th><th class="th">Status</th></tr></thead>
            <tbody>
            @forelse ($barangMenipis as $b)
                <tr>
                    <td class="td">{{ $b->nama }}</td>
                    <td class="td">{{ $b->stok }} {{ $b->satuan }}</td>
                    <td class="td">{{ $b->safety_stock }}</td>
                    <td class="td">@include('partials.status', ['barang' => $b])</td>
                </tr>
            @empty
                <tr><td colspan="4" class="td text-center text-emerald-600">Semua stok dalam kondisi aman.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="mb-3 font-semibold">Mutasi Terbaru</h2>
        <table class="w-full">
            <thead><tr><th class="th">Waktu</th><th class="th">Barang</th><th class="th">Jenis</th><th class="th">Jumlah</th></tr></thead>
            <tbody>
            @forelse ($mutasiTerbaru as $m)
                <tr>
                    <td class="td">{{ $m->created_at->format('d/m H:i') }}</td>
                    <td class="td">{{ $m->barang->nama }}</td>
                    <td class="td"><span class="{{ $m->jenis === 'masuk' ? 'text-emerald-600' : 'text-red-600' }} font-semibold">{{ strtoupper($m->jenis) }}</span></td>
                    <td class="td">{{ $m->jumlah }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="td text-center text-slate-400">Belum ada transaksi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
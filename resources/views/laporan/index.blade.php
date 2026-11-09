@extends('layouts.app')
@section('title', 'Laporan Inventory')

@section('content')
@php
    $jenis = old('jenis', request('jenis'));
    $dari = old('dari', request('dari', now()->startOfMonth()->toDateString()));
    $sampai = old('sampai', request('sampai', now()->toDateString()));
    $jm = old('jenis_mutasi', request('jenis_mutasi', 'semua'));
@endphp

<div class="card mb-6">
    @if (auth()->user()->hasRole('auditor'))
        <p class="mb-3 rounded bg-slate-100 px-3 py-2 text-xs text-slate-600">Mode read-only: Auditor hanya dapat melihat dan mengekspor laporan.</p>
    @endif
    <form method="GET" action="{{ route('laporan.index') }}" class="grid items-end gap-3 sm:grid-cols-5">
        <div class="sm:col-span-2">
            <label class="label">Jenis Laporan</label>
            <select name="jenis" class="input" required>
                <option value="">— Pilih —</option>
                <option value="persediaan" @selected($jenis === 'persediaan')>Persediaan Barang</option>
                <option value="mutasi" @selected($jenis === 'mutasi')>Mutasi Barang Masuk-Keluar</option>
                <option value="stock_opname" @selected($jenis === 'stock_opname')>Hasil Stock Opname</option>
            </select>
        </div>
        <div><label class="label">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="input"></div>
        <div><label class="label">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="input"></div>
        <div>
            <label class="label">Jenis Mutasi</label>
            <select name="jenis_mutasi" class="input">
                <option value="semua" @selected($jm === 'semua')>Semua</option>
                <option value="masuk" @selected($jm === 'masuk')>Masuk</option>
                <option value="keluar" @selected($jm === 'keluar')>Keluar</option>
            </select>
        </div>
        <div class="sm:col-span-5"><button class="btn-primary">Tampilkan Laporan</button></div>
    </form>
</div>

@if ($laporan)
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold">{{ $laporan['judul'] }}</h2>
                <div class="text-sm text-slate-500">{{ $laporan['periode'] }} &middot; {{ count($laporan['baris']) }} baris</div>
            </div>
            <div class="flex gap-2">
                @php($q = request()->query())
                <a href="{{ route('laporan.export', $q + ['format' => 'pdf']) }}" class="btn-danger">Ekspor PDF</a>
                <a href="{{ route('laporan.export', $q + ['format' => 'excel']) }}" class="btn-primary">Ekspor Excel</a>
            </div>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr>@foreach ($laporan['kolom'] as $k)<th class="th">{{ $k }}</th>@endforeach</tr></thead>
            <tbody>
            @foreach ($laporan['baris'] as $baris)
                <tr>@foreach ($baris as $sel)<td class="td">{{ $sel }}</td>@endforeach</tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endif
@endsection

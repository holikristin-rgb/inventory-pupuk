@extends('layouts.app')
@section('title', 'Data Barang')

@section('content')
@php($staf = auth()->user()->hasRole('staf_gudang'))
<div class="card">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama / jenis" class="input w-64">
            <button class="btn-secondary">Cari</button>
        </form>
        @if ($staf)
            <a href="{{ route('barang.create') }}" class="btn-primary">+ Tambah Barang</a>
        @endif
    </div>

    <div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr>
                <th class="th">Kode</th><th class="th">Nama</th><th class="th">Jenis</th><th class="th">Stok</th>
                <th class="th">Safety Stock</th><th class="th">Status</th><th class="th text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($barang as $b)
            <tr>
                <td class="td font-mono text-xs">{{ $b->kode_barang }}</td>
                <td class="td"><a href="{{ route('barang.show', $b) }}" class="font-medium text-emerald-700 hover:underline">{{ $b->nama }}</a></td>
                <td class="td">{{ $b->jenis ?: '-' }}</td>
                <td class="td">{{ $b->stok }} {{ $b->satuan }}</td>
                <td class="td">{{ $b->safety_stock }}</td>
                <td class="td">@include('partials.status', ['barang' => $b])</td>
                <td class="td">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('barang.show', $b) }}" class="btn-secondary">Detail</a>
                        @if ($staf)
                            <a href="{{ route('barang.edit', $b) }}" class="btn-secondary">Ubah</a>
                            <form method="POST" action="{{ route('barang.destroy', $b) }}" onsubmit="return confirm('Hapus barang {{ $b->nama }}?')">
                                @csrf @method('DELETE')
                                <button class="btn-danger">Hapus</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="td text-center text-slate-400">Data barang tidak ditemukan.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $barang->links() }}</div>
</div>
@endsection

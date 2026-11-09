@extends('layouts.app')
@section('title', 'Peringatan Safety Stock')

@section('content')
@if ($notifikasi->isNotEmpty())
    <div class="mb-6 space-y-2">
        @foreach ($notifikasi as $n)
            <a href="{{ route('safety-stock.index', ['barang' => $n->data['barang_id']]) }}"
               class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 hover:bg-red-100">
                <span class="text-lg">⚠️</span>
                <div>
                    <div>{{ $n->data['pesan'] }}</div>
                    <div class="text-xs text-red-600">{{ $n->created_at->diffForHumans() }}</div>
                </div>
            </a>
        @endforeach
    </div>
@endif

<div class="card">
    <h2 class="mb-1 font-semibold">Barang yang Perlu Pengadaan Ulang (Re-order)</h2>
    <p class="mb-4 text-sm text-slate-500">Barang dengan stok pada atau di bawah batas Safety Stock. Daftar ini diperbarui otomatis setiap ada transaksi.</p>

    <div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr>
                <th class="th">Barang</th><th class="th">Stok</th><th class="th">Safety Stock</th><th class="th">Reorder Point</th>
                <th class="th">Lead Time</th><th class="th">Pemakaian Rata-rata/Hari</th><th class="th">Saran Pengadaan</th><th class="th">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($barang as $b)
            <tr class="{{ $sorot === $b->id ? 'bg-amber-50 ring-2 ring-amber-300' : '' }}">
                <td class="td"><a class="font-medium text-emerald-700 hover:underline" href="{{ route('barang.show', $b) }}">{{ $b->nama }}</a></td>
                <td class="td font-semibold text-red-600">{{ $b->stok }} {{ $b->satuan }}</td>
                <td class="td">{{ $b->safety_stock }}</td>
                <td class="td">{{ $b->reorder_point }}</td>
                <td class="td">{{ $b->lead_time }} hari</td>
                <td class="td">{{ $b->pemakaian_rata_rata }}</td>
                <td class="td font-semibold">{{ $b->saran_pengadaan }} {{ $b->satuan }}</td>
                <td class="td">@include('partials.status', ['barang' => $b])</td>
            </tr>
        @empty
            <tr><td colspan="8" class="td text-center text-emerald-600">Status stok aman. Tidak ada barang yang perlu pengadaan ulang.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection

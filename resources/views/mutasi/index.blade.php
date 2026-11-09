@extends('layouts.app')
@section('title', $jenis === 'masuk' ? 'Riwayat Barang Masuk' : 'Riwayat Barang Keluar')

@section('content')
<div class="card">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold">Riwayat Mutasi {{ $jenis === 'masuk' ? 'Masuk' : 'Keluar' }}</h2>
        @if (auth()->user()->hasRole('staf_gudang'))
            <a href="{{ route('barang-' . $jenis . '.create') }}" class="btn-primary">+ Catat Barang {{ ucfirst($jenis) }}</a>
        @endif
    </div>
    <div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr>
                <th class="th">Waktu</th><th class="th">Kode</th><th class="th">Barang</th><th class="th">Jumlah</th>
                <th class="th">Stok Sebelum → Sesudah</th>
                <th class="th">{{ $jenis === 'masuk' ? 'Lokasi / Pemasok' : 'Diambil dari / Tujuan' }}</th>
                <th class="th">Petugas</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($mutasi as $m)
            <tr>
                <td class="td whitespace-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td class="td font-mono text-xs">{{ $m->kode_transaksi }}</td>
                <td class="td">{{ $m->barang->nama }}</td>
                <td class="td font-semibold {{ $jenis === 'masuk' ? 'text-emerald-600' : 'text-red-600' }}">{{ $jenis === 'masuk' ? '+' : '-' }}{{ $m->jumlah }}</td>
                <td class="td">{{ $m->stok_sebelum }} → {{ $m->stok_sesudah }}</td>
                <td class="td text-xs">
                    @if ($jenis === 'masuk')
                        {{ $m->lokasi?->kode ?? '-' }}
                    @else
                        {{ collect($m->rincian_lokasi)->map(fn ($r) => $r['lokasi'] . ' (' . $r['jumlah'] . ')')->implode('; ') ?: '-' }}
                    @endif
                    @if ($m->pihak) <div class="text-slate-500">{{ $m->pihak }}</div> @endif
                </td>
                <td class="td">{{ $m->user->name }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="td text-center text-slate-400">Belum ada transaksi.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $mutasi->links() }}</div>
</div>
@endsection

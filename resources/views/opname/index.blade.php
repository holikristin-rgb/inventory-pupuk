@extends('layouts.app')
@section('title', 'Stock Opname Digital')

@section('content')
<div class="card">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold">Dokumen Hasil Stock Opname</h2>
        @if (auth()->user()->hasRole('staf_gudang'))
            <a href="{{ route('opname.create') }}" class="btn-primary">+ Opname Baru</a>
        @endif
    </div>
    <table class="w-full">
        <thead><tr><th class="th">Tanggal</th><th class="th">Kode</th><th class="th">Petugas</th><th class="th">Barang Diperiksa</th><th class="th">Ada Selisih</th><th class="th"></th></tr></thead>
        <tbody>
        @forelse ($opname as $o)
            <tr>
                <td class="td">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                <td class="td font-mono text-xs">{{ $o->kode }}</td>
                <td class="td">{{ $o->user->name }}</td>
                <td class="td">{{ $o->detail_count }}</td>
                <td class="td"><span class="{{ $o->selisih_count ? 'font-semibold text-red-600' : 'text-emerald-600' }}">{{ $o->selisih_count }}</span></td>
                <td class="td text-right"><a href="{{ route('opname.show', $o) }}" class="btn-secondary">Lihat</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="td text-center text-slate-400">Belum ada stock opname.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $opname->links() }}</div>
</div>
@endsection

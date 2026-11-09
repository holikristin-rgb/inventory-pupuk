@extends('layouts.app')
@section('title', 'Detail Barang')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="card lg:col-span-2">
        <div class="mb-4 flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold">{{ $barang->nama }}</h2>
                <div class="font-mono text-xs text-slate-500">{{ $barang->kode_barang }}</div>
            </div>
            @include('partials.status', ['barang' => $barang])
        </div>

        <dl class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
            <div><dt class="text-slate-500">Stok</dt><dd class="text-lg font-semibold">{{ $barang->stok }} {{ $barang->satuan }}</dd></div>
            <div><dt class="text-slate-500">Safety Stock</dt><dd class="text-lg font-semibold">{{ $barang->safety_stock }}</dd></div>
            <div><dt class="text-slate-500">Reorder Point</dt><dd class="text-lg font-semibold">{{ $barang->reorder_point }}</dd></div>
            <div><dt class="text-slate-500">Pemakaian Rata-rata/Hari</dt><dd>{{ $barang->pemakaian_rata_rata }}</dd></div>
            <div><dt class="text-slate-500">Pemakaian Maks/Hari</dt><dd>{{ $barang->pemakaian_maksimum }}</dd></div>
            <div><dt class="text-slate-500">Lead Time</dt><dd>{{ $barang->lead_time }} hari</dd></div>
        </dl>

        <h3 class="mb-2 mt-6 font-semibold">Lokasi Penyimpanan</h3>
        <ul class="space-y-1 text-sm">
            @forelse ($barang->lokasi as $l)
                <li class="rounded bg-slate-50 px-3 py-2">{{ $l->label }} — <b>{{ $l->jumlah }}</b> {{ $barang->satuan }}</li>
            @empty
                <li class="text-slate-400">Barang belum berada di rak manapun.</li>
            @endforelse
        </ul>

        <h3 class="mb-2 mt-6 font-semibold">Mutasi Terakhir</h3>
        <table class="w-full">
            <thead><tr><th class="th">Waktu</th><th class="th">Jenis</th><th class="th">Jumlah</th><th class="th">Petugas</th></tr></thead>
            <tbody>
            @forelse ($mutasi as $m)
                <tr>
                    <td class="td">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td class="td">{{ strtoupper($m->jenis) }}</td>
                    <td class="td">{{ $m->jumlah }}</td>
                    <td class="td">{{ $m->user->name }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="td text-center text-slate-400">Belum ada mutasi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card text-center">
        <h3 class="mb-3 font-semibold">QR Code Barang</h3>
        <div id="qrcode" class="mx-auto flex justify-center"></div>
        <p class="mt-3 text-xs text-slate-500">Cetak dan tempel pada kemasan. Isi QR = kode barang.</p>
        <button onclick="window.print()" class="btn-secondary mt-3">Cetak</button>
    </div>
</div>
<a href="{{ route('barang.index') }}" class="btn-secondary mt-4">&larr; Kembali</a>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('qrcode'), { text: @json($barang->kode_barang), width: 160, height: 160 });
</script>
@endpush

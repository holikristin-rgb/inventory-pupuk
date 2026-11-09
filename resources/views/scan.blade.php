@extends('layouts.app')
@section('title', 'Pemindaian Barcode/QR Code')

@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="card">
        @include('partials.scanner')
    </div>

    <div class="card">
        <h2 class="mb-3 font-semibold">Data Barang Hasil Pindai</h2>
        <div id="hasil" class="hidden space-y-2 text-sm">
            <div class="text-lg font-bold" id="h-nama"></div>
            <div class="font-mono text-xs text-slate-500" id="h-kode"></div>
            <div>Stok: <b id="h-stok"></b> &middot; Safety Stock: <b id="h-ss"></b></div>
            <div>Status: <b id="h-status"></b></div>
            <div class="font-medium">Lokasi rak:</div>
            <ul id="h-lokasi" class="list-inside list-disc text-slate-600"></ul>
        </div>
        <p id="kosong" class="text-sm text-slate-400">Belum ada barang yang dipindai.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('barang-ditemukan', e => {
        const b = e.detail;
        document.getElementById('kosong').classList.add('hidden');
        document.getElementById('hasil').classList.remove('hidden');
        document.getElementById('h-nama').textContent = b.nama;
        document.getElementById('h-kode').textContent = b.kode_barang;
        document.getElementById('h-stok').textContent = b.stok + ' ' + b.satuan;
        document.getElementById('h-ss').textContent = b.safety_stock;
        document.getElementById('h-status').textContent = b.status;
        const ul = document.getElementById('h-lokasi');
        ul.innerHTML = '';
        if (!b.lokasi.length) { ul.innerHTML = '<li>Belum ada di rak manapun</li>'; }
        b.lokasi.forEach(l => { const li = document.createElement('li'); li.textContent = l.label + ' — ' + l.jumlah; ul.appendChild(li); });
    });
    document.addEventListener('barang-gagal', () => {
        document.getElementById('hasil').classList.add('hidden');
        document.getElementById('kosong').classList.remove('hidden');
    });
</script>
@endpush

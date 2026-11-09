@extends('layouts.app')
@section('title', 'Barang Keluar')

@section('content')
@php($lama = old('barang_id') ? \App\Models\Barang::find(old('barang_id')) : null)
<div class="grid gap-6 lg:grid-cols-2">
    <div class="card">
        <h2 class="mb-3 font-semibold">1. Pindai Barcode/QR Code Barang</h2>
        @include('partials.scanner')

        <div id="info-barang" class="mt-4 hidden rounded-lg bg-emerald-50 p-4 text-sm">
            <div class="text-base font-semibold" id="ib-nama"></div>
            <div class="text-slate-600">Sisa stok: <b id="ib-stok"></b></div>
            <ul id="ib-lokasi" class="mt-2 list-inside list-disc text-slate-600"></ul>
        </div>
    </div>

    <div class="card">
        <h2 class="mb-3 font-semibold">2. Konfirmasi Pengeluaran</h2>
        <form method="POST" action="{{ route('barang-keluar.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="barang_id" id="barang_id" value="{{ old('barang_id') }}">

            <div>
                <label class="label">Jumlah Keluar *</label>
                <input type="number" min="1" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" class="input" required>
                <p class="mt-1 text-xs text-slate-500">Barang diambil dari slot terlama terlebih dahulu (FIFO).</p>
            </div>
            <div>
                <label class="label">Tujuan / Penerima</label>
                <input type="text" name="tujuan" value="{{ old('tujuan') }}" class="input">
            </div>
            <div>
                <label class="label">Keterangan</label>
                <textarea name="keterangan" rows="2" class="input">{{ old('keterangan') }}</textarea>
            </div>
            <div class="flex gap-2">
                <button class="btn-primary" id="btn-simpan" @disabled(! $lama)>Konfirmasi</button>
                <a href="{{ route('barang-keluar.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function pilihBarang(b) {
        document.getElementById('barang_id').value = b.id;
        document.getElementById('ib-nama').textContent = b.nama;
        document.getElementById('ib-stok').textContent = b.stok + ' ' + b.satuan;
        document.getElementById('jumlah').max = b.stok;
        const ul = document.getElementById('ib-lokasi');
        ul.innerHTML = '';
        (b.lokasi || []).forEach(l => {
            const li = document.createElement('li');
            li.textContent = l.label + ' — ' + l.jumlah;
            ul.appendChild(li);
        });
        document.getElementById('info-barang').classList.remove('hidden');
        document.getElementById('btn-simpan').disabled = false;
        document.getElementById('jumlah').focus();
    }
    document.addEventListener('barang-ditemukan', e => pilihBarang(e.detail));
    document.addEventListener('barang-gagal', () => {
        document.getElementById('info-barang').classList.add('hidden');
    });
    @if ($lama)
        pilihBarang(@json(['id' => $lama->id, 'nama' => $lama->nama, 'stok' => $lama->stok, 'satuan' => $lama->satuan]));
    @endif
</script>
@endpush

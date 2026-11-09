@extends('layouts.app')
@section('title', 'Barang Masuk')

@section('content')
@php($lama = old('barang_id') ? \App\Models\Barang::find(old('barang_id')) : null)
<div class="grid gap-6 lg:grid-cols-2">
    <div class="card">
        <h2 class="mb-3 font-semibold">1. Pindai Barcode/QR Code Kemasan</h2>
        @include('partials.scanner')

        <div id="info-barang" class="mt-4 hidden rounded-lg bg-emerald-50 p-4 text-sm">
            <div class="text-base font-semibold" id="ib-nama"></div>
            <div class="text-slate-600">Stok saat ini: <b id="ib-stok"></b></div>
        </div>
    </div>

    <div class="card">
        <h2 class="mb-3 font-semibold">2. Formulir Penerimaan Barang</h2>
        <form method="POST" action="{{ route('barang-masuk.store') }}" class="space-y-4" id="form-masuk">
            @csrf
            <input type="hidden" name="barang_id" id="barang_id" value="{{ old('barang_id') }}">

            <div>
                <label class="label">Lokasi Rak Tujuan *</label>
                <select name="lokasi_id" id="lokasi_id" class="input" required>
                    <option value="">— Pilih slot —</option>
                    @foreach ($lokasi as $l)
                        <option value="{{ $l->id }}" data-barang="{{ $l->barang_id }}" data-jumlah="{{ $l->jumlah }}"
                                @selected(old('lokasi_id') == $l->id)>
                            {{ $l->label }} — isi {{ $l->jumlah }}/{{ $l->kapasitas }}{{ $l->barang ? ' (' . $l->barang->nama . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Hanya slot kosong atau slot berisi barang yang sama yang dapat dipilih.</p>
            </div>
            <div>
                <label class="label">Jumlah Masuk *</label>
                <input type="number" min="1" name="jumlah" value="{{ old('jumlah') }}" class="input" required>
            </div>
            <div>
                <label class="label">Pemasok</label>
                <input type="text" name="pemasok" value="{{ old('pemasok') }}" class="input">
            </div>
            <div>
                <label class="label">Keterangan</label>
                <textarea name="keterangan" rows="2" class="input">{{ old('keterangan') }}</textarea>
            </div>
            <div class="flex gap-2">
                <button class="btn-primary" id="btn-simpan" @disabled(! $lama)>Simpan</button>
                <a href="{{ route('barang-masuk.index') }}" class="btn-secondary">Batal</a>
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
        document.getElementById('info-barang').classList.remove('hidden');
        document.getElementById('btn-simpan').disabled = false;

        // Hanya slot kosong / slot barang yang sama yang bisa dipilih
        const sel = document.getElementById('lokasi_id');
        [...sel.options].forEach(o => {
            if (!o.value) return;
            o.disabled = Number(o.dataset.jumlah) > 0 && Number(o.dataset.barang) !== Number(b.id);
        });
        if (sel.selectedOptions[0]?.disabled) sel.value = '';
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

@extends('layouts.app')
@section('title', $barang->exists ? 'Ubah Barang' : 'Tambah Barang')

@section('content')
<div class="card max-w-3xl">
    <form method="POST" action="{{ $barang->exists ? route('barang.update', $barang) : route('barang.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        @if ($barang->exists) @method('PUT') @endif

        <div>
            <label class="label">Kode Barang (isi Barcode/QR)</label>
            <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" class="input" placeholder="Kosongkan untuk dibuat otomatis">
        </div>
        <div>
            <label class="label">Nama Barang *</label>
            <input type="text" name="nama" value="{{ old('nama', $barang->nama) }}" class="input" required>
        </div>
        <div>
            <label class="label">Jenis Pupuk</label>
            <input type="text" name="jenis" value="{{ old('jenis', $barang->jenis) }}" class="input" placeholder="Urea, NPK, ZA ...">
        </div>
        <div>
            <label class="label">Satuan *</label>
            <input type="text" name="satuan" value="{{ old('satuan', $barang->satuan) }}" class="input" required>
        </div>

        <div class="sm:col-span-2 mt-2 border-t pt-4">
            <h3 class="font-semibold">Parameter Safety Stock</h3>
            <p class="text-xs text-slate-500">Safety Stock = (pemakaian maksimum − pemakaian rata-rata) × lead time. Dihitung otomatis saat disimpan.</p>
        </div>
        <div>
            <label class="label">Pemakaian Rata-rata per Hari *</label>
            <input type="number" step="0.01" min="0" name="pemakaian_rata_rata" value="{{ old('pemakaian_rata_rata', $barang->pemakaian_rata_rata) }}" class="input" required>
        </div>
        <div>
            <label class="label">Pemakaian Maksimum per Hari *</label>
            <input type="number" step="0.01" min="0" name="pemakaian_maksimum" value="{{ old('pemakaian_maksimum', $barang->pemakaian_maksimum) }}" class="input" required>
        </div>
        <div>
            <label class="label">Lead Time Pengadaan (hari) *</label>
            <input type="number" min="0" name="lead_time" value="{{ old('lead_time', $barang->lead_time) }}" class="input" required>
        </div>
        @if ($barang->exists)
            <div>
                <label class="label">Safety Stock Saat Ini</label>
                <input type="text" value="{{ $barang->safety_stock }} {{ $barang->satuan }}" class="input bg-slate-100" disabled>
            </div>
        @endif
        <div class="sm:col-span-2">
            <label class="label">Keterangan</label>
            <textarea name="keterangan" rows="2" class="input">{{ old('keterangan', $barang->keterangan) }}</textarea>
        </div>

        <div class="sm:col-span-2 flex gap-2">
            <button class="btn-primary">Simpan</button>
            <a href="{{ route('barang.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

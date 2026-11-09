@extends('layouts.app')
@section('title', 'Atur Lokasi Penyimpanan')

@section('content')
<div class="card max-w-xl">
    <h2 class="mb-1 font-semibold">{{ $lokasi->label }}</h2>
    <p class="mb-4 text-sm text-slate-500">Isi slot saat ini: {{ $lokasi->jumlah }} dari kapasitas {{ $lokasi->kapasitas }}.</p>

    <form method="POST" action="{{ route('lokasi.update', $lokasi) }}" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="label">Alokasi Barang</label>
            <select name="barang_id" class="input" @disabled($lokasi->jumlah > 0)>
                <option value="">— Kosong / belum dialokasikan —</option>
                @foreach ($barang as $b)
                    <option value="{{ $b->id }}" @selected(old('barang_id', $lokasi->barang_id) == $b->id)>{{ $b->nama }} ({{ $b->kode_barang }})</option>
                @endforeach
            </select>
            @if ($lokasi->jumlah > 0)
                <input type="hidden" name="barang_id" value="{{ $lokasi->barang_id }}">
                <p class="mt-1 text-xs text-amber-600">Slot masih berisi stok, alokasi tidak dapat diubah.</p>
            @endif
        </div>
        <div>
            <label class="label">Kapasitas</label>
            <input type="number" min="1" name="kapasitas" value="{{ old('kapasitas', $lokasi->kapasitas) }}" class="input" required>
        </div>
        <div class="flex gap-2">
            <button class="btn-primary">Simpan</button>
            <a href="{{ route('lokasi.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

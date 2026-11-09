@extends('layouts.app')
@section('title', 'Kelola Lokasi Penyimpanan')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="card lg:col-span-1 h-fit">
        <h2 class="mb-3 font-semibold">Tambah Slot</h2>
        <form method="POST" action="{{ route('lokasi.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-3 gap-2">
                <div><label class="label">Lorong</label><input name="lorong" value="{{ old('lorong') }}" class="input uppercase" maxlength="10" required></div>
                <div><label class="label">Rak</label><input type="number" min="1" name="rak" value="{{ old('rak') }}" class="input" required></div>
                <div><label class="label">Tingkat</label><input type="number" min="1" name="tingkat" value="{{ old('tingkat') }}" class="input" required></div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div><label class="label">Jumlah Slot</label><input type="number" min="1" max="50" name="jumlah_slot" value="{{ old('jumlah_slot', 4) }}" class="input" required></div>
                <div><label class="label">Kapasitas/Slot</label><input type="number" min="1" name="kapasitas" value="{{ old('kapasitas', 100) }}" class="input" required></div>
            </div>
            <button class="btn-primary">Tambah</button>
        </form>
    </div>

    <div class="card lg:col-span-2">
        <form method="GET" class="mb-4 flex items-center gap-2">
            <select name="lorong" class="input w-40" onchange="this.form.submit()">
                <option value="">Semua Lorong</option>
                @foreach ($daftarLorong as $l)
                    <option value="{{ $l }}" @selected(request('lorong') === $l)>Lorong {{ $l }}</option>
                @endforeach
            </select>
        </form>

        <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr><th class="th">Lokasi</th><th class="th">Alokasi Barang</th><th class="th">Isi / Kapasitas</th><th class="th text-right">Aksi</th></tr></thead>
            <tbody>
            @forelse ($lokasi as $l)
                <tr>
                    <td class="td font-mono text-xs">{{ $l->kode }}</td>
                    <td class="td">{{ $l->barang?->nama ?? '—' }}</td>
                    <td class="td">{{ $l->jumlah }} / {{ $l->kapasitas }}</td>
                    <td class="td">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('lokasi.edit', $l) }}" class="btn-secondary">Atur</a>
                            <form method="POST" action="{{ route('lokasi.destroy', $l) }}" onsubmit="return confirm('Hapus slot ini?')">
                                @csrf @method('DELETE')
                                <button class="btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="td text-center text-slate-400">Belum ada slot. Tambahkan melalui form di samping.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-4">{{ $lokasi->links() }}</div>
    </div>
</div>
@endsection

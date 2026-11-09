@extends('layouts.app')
@section('title', 'Monitoring Stok Real-time')

@section('content')
<div class="card">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2" id="filter">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode / nama barang" class="input w-64">
            <select name="status" class="input w-48" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="aman" @selected(request('status') === 'aman')>Aman</option>
                <option value="menipis" @selected(request('status') === 'menipis')>Di bawah Safety Stock</option>
                <option value="habis" @selected(request('status') === 'habis')>Habis</option>
            </select>
            <button class="btn-secondary">Cari</button>
        </form>
        <div class="text-xs text-slate-500">Diperbarui otomatis tiap 15 detik &middot; <span id="waktu">{{ now()->format('H:i:s') }}</span></div>
    </div>

    <div id="tabel-stok" class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr><th class="th">Kode</th><th class="th">Barang</th><th class="th">Jumlah Stok</th><th class="th">Safety Stock</th><th class="th">Lokasi Rak</th><th class="th">Status</th></tr>
            </thead>
            <tbody>
            @forelse ($barang as $b)
                <tr class="{{ $b->status !== 'aman' ? 'bg-red-50' : '' }}">
                    <td class="td font-mono text-xs">{{ $b->kode_barang }}</td>
                    <td class="td font-medium">{{ $b->nama }}</td>
                    <td class="td">{{ $b->stok }} {{ $b->satuan }}</td>
                    <td class="td">{{ $b->safety_stock }}</td>
                    <td class="td text-xs">
                        @forelse ($b->lokasi as $l)
                            <div>{{ $l->kode }} ({{ $l->jumlah }})</div>
                        @empty
                            -
                        @endforelse
                    </td>
                    <td class="td">@include('partials.status', ['barang' => $b])</td>
                </tr>
            @empty
                <tr><td colspan="6" class="td text-center text-slate-400">Data tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pembaruan stok berkala (tanpa reload halaman) dengan mengganti isi tabel.
    setInterval(async () => {
        if (document.activeElement && document.activeElement.tagName === 'INPUT') return;
        const res = await fetch(location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const html = new DOMParser().parseFromString(await res.text(), 'text/html');
        const baru = html.getElementById('tabel-stok');
        if (baru) {
            document.getElementById('tabel-stok').innerHTML = baru.innerHTML;
            document.getElementById('waktu').textContent = new Date().toLocaleTimeString('id-ID');
        }
    }, 15000);
</script>
@endpush

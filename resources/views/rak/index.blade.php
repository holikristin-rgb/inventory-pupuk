@extends('layouts.app')
@section('title', 'Visualisasi Lokasi Rak')

@section('content')
<div class="mb-4 flex flex-wrap items-center gap-4 text-sm">
    <span class="flex items-center gap-1"><span class="inline-block h-4 w-4 rounded bg-slate-200"></span> Kosong</span>
    <span class="flex items-center gap-1"><span class="inline-block h-4 w-4 rounded bg-emerald-300"></span> Terisi (aman)</span>
    <span class="flex items-center gap-1"><span class="inline-block h-4 w-4 rounded bg-red-400"></span> Di bawah Safety Stock</span>
    <span class="text-slate-500">Klik slot untuk melihat rincian.</span>
</div>

@forelse ($peta as $lorong => $raks)
    <div class="card mb-6">
        <h2 class="mb-4 text-lg font-bold">Lorong {{ $lorong }}</h2>
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($raks as $rak => $tingkats)
                <div class="rounded-lg border border-slate-300 bg-slate-50 p-3">
                    <div class="mb-2 text-sm font-semibold">Rak {{ $rak }}</div>
                    <div class="space-y-2">
                        @foreach ($tingkats as $tingkat => $slots)
                            <div class="flex items-center gap-2">
                                <span class="w-8 shrink-0 text-xs text-slate-500">T{{ $tingkat }}</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($slots as $s)
                                        @php
                                            $kelas = $s->kosong ? 'bg-slate-200 text-slate-500'
                                                : ($s->barang && $s->barang->status !== 'aman' ? 'bg-red-400 text-white' : 'bg-emerald-300 text-emerald-900');
                                        @endphp
                                        <button type="button" data-slot="{{ $s->id }}"
                                                class="slot relative h-12 w-12 rounded text-xs font-semibold {{ $kelas }} hover:ring-2 hover:ring-slate-700"
                                                title="{{ $s->label }}">
                                            S{{ $s->slot }}
                                            @if (! $s->kosong && $s->barang && $s->barang->status !== 'aman')
                                                <span class="absolute -right-1 -top-1 rounded-full bg-red-700 px-1 text-[10px] leading-4 text-white">!</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="card text-center text-slate-400">Belum ada data lokasi penyimpanan.</div>
@endforelse

{{-- Pop-up rincian slot --}}
<div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-sm rounded-xl bg-white p-5 shadow-xl">
        <div class="mb-3 flex items-start justify-between">
            <h3 class="font-semibold" id="m-judul">Rincian Slot</h3>
            <button type="button" id="m-tutup" class="text-xl leading-none text-slate-400 hover:text-slate-700">&times;</button>
        </div>
        <div id="m-isi" class="space-y-1 text-sm"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('modal');
    const isi = document.getElementById('m-isi');
    const baris = (k, v) => `<div class="flex justify-between gap-4"><span class="text-slate-500">${k}</span><span class="font-medium text-right">${v ?? '-'}</span></div>`;

    document.querySelectorAll('.slot').forEach(btn => btn.addEventListener('click', async () => {
        const res = await fetch(`{{ url('/rak/slot') }}/` + btn.dataset.slot, { headers: { 'Accept': 'application/json' } });
        const d = await res.json();
        document.getElementById('m-judul').textContent = d.lokasi;
        isi.innerHTML = d.kosong
            ? `<p class="text-slate-500">${d.pesan}</p>`
            : baris('Jenis pupuk', d.jenis_pupuk) + baris('Volume stok', d.volume_stok) + baris('Kapasitas slot', d.kapasitas)
              + baris('Tanggal penerimaan', d.tanggal_penerimaan) + baris('Status', d.status);
        modal.classList.replace('hidden', 'flex');
    }));
    const tutup = () => modal.classList.replace('flex', 'hidden');
    document.getElementById('m-tutup').addEventListener('click', tutup);
    modal.addEventListener('click', e => { if (e.target === modal) tutup(); });
</script>
@endpush

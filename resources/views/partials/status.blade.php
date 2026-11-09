@php
    $kelas = match ($barang->status) {
        'habis'   => 'bg-red-100 text-red-700',
        'menipis' => 'bg-amber-100 text-amber-700',
        default   => 'bg-emerald-100 text-emerald-700',
    };
@endphp
<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $kelas }}">
    {{ $barang->status_label }}
</span>

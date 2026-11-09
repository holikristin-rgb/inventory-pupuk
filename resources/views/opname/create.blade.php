@extends('layouts.app')
@section('title', 'Stock Opname Digital')

@section('content')
<div class="card mb-6">
    <h2 class="mb-3 font-semibold">Pindai Barcode Pupuk di Rak</h2>
    @include('partials.scanner')
</div>

<form method="POST" action="{{ route('opname.store') }}" class="card" onsubmit="return cekForm()">
    @csrf
    <h2 class="mb-3 font-semibold">Daftar Verifikasi Stok Fisik</h2>

    <div class="overflow-x-auto">
    <table class="w-full" id="tabel">
        <thead>
            <tr><th class="th">Barang</th><th class="th">Stok Sistem</th><th class="th w-32">Stok Fisik</th><th class="th">Selisih</th><th class="th">Keterangan Selisih</th><th class="th"></th></tr>
        </thead>
        <tbody id="baris"></tbody>
    </table>
    </div>
    <p id="kosong" class="py-6 text-center text-sm text-slate-400">Belum ada barang. Pindai barcode pupuk untuk menambahkan.</p>

    <div class="mt-4">
        <label class="label">Catatan Umum (opsional)</label>
        <textarea name="catatan" rows="2" class="input">{{ old('catatan') }}</textarea>
    </div>

    <div class="mt-4 flex gap-2">
        <button class="btn-primary">Finalisasi Opname</button>
        <a href="{{ route('opname.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let idx = 0;
    const tbody = document.getElementById('baris');
    const kosong = document.getElementById('kosong');

    function hitung(tr) {
        const sistem = Number(tr.dataset.sistem);
        const fisik = tr.querySelector('.fisik').value;
        const selisih = fisik === '' ? 0 : Number(fisik) - sistem;
        const sel = tr.querySelector('.selisih');
        sel.textContent = (selisih > 0 ? '+' : '') + selisih;
        sel.className = 'selisih font-semibold ' + (selisih === 0 ? 'text-emerald-600' : 'text-red-600');
        const ket = tr.querySelector('.ket');
        ket.required = selisih !== 0;
        ket.placeholder = selisih !== 0 ? 'Wajib diisi (ada selisih)' : 'Tidak ada selisih';
    }

    document.addEventListener('barang-ditemukan', e => {
        const b = e.detail;
        const ada = tbody.querySelector(`tr[data-id="${b.id}"]`);
        if (ada) { ada.querySelector('.fisik').focus(); return; }

        const i = idx++;
        const tr = document.createElement('tr');
        tr.dataset.id = b.id;
        tr.dataset.sistem = b.stok;
        tr.innerHTML = `
            <td class="td"><div class="font-medium"></div><div class="text-xs text-slate-500"></div>
                <input type="hidden" name="items[${i}][barang_id]" value="${b.id}"></td>
            <td class="td">${b.stok} ${b.satuan}</td>
            <td class="td"><input type="number" min="0" name="items[${i}][stok_fisik]" class="input fisik" required></td>
            <td class="td"><span class="selisih font-semibold text-emerald-600">0</span></td>
            <td class="td"><input type="text" name="items[${i}][keterangan]" class="input ket" placeholder="Tidak ada selisih"></td>
            <td class="td"><button type="button" class="btn-danger hapus">&times;</button></td>`;
        tr.querySelector('.font-medium').textContent = b.nama;
        tr.querySelector('.text-xs').textContent = b.kode_barang;
        tr.querySelector('.fisik').addEventListener('input', () => hitung(tr));
        tr.querySelector('.hapus').addEventListener('click', () => { tr.remove(); toggle(); });
        tbody.appendChild(tr);
        toggle();
        tr.querySelector('.fisik').focus();
    });

    function toggle() { kosong.classList.toggle('hidden', tbody.children.length > 0); }

    function cekForm() {
        if (!tbody.children.length) { alert('Pindai minimal satu barang terlebih dahulu.'); return false; }
        return confirm('Finalisasi stock opname? Dokumen hasil opname akan disimpan.');
    }
</script>
@endpush

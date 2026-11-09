{{--
    Komponen pemindaian Barcode/QR Code.
    - Scanner USB (keyboard wedge): fokus ke kolom, pindai, otomatis Enter.
    - Kamera HP/laptop: tombol "Buka Kamera" (butuh HTTPS atau localhost).
    Hasil pencarian dikirim lewat event dokumen:
        document.addEventListener('barang-ditemukan', e => e.detail)
        document.addEventListener('barang-gagal', e => e.detail.pesan)
--}}
<div class="space-y-3">
    <div class="flex gap-2">
        <input type="text" id="kode_scan" class="input" placeholder="Pindai atau ketik kode barang, lalu tekan Enter" autocomplete="off" autofocus>
        <button type="button" id="btn-cari" class="btn-primary">Cari</button>
        <button type="button" id="btn-kamera" class="btn-secondary whitespace-nowrap">Buka Kamera</button>
    </div>
    <div id="reader" class="hidden max-w-sm overflow-hidden rounded-lg"></div>
    <div id="scan-pesan" class="text-sm"></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
    const input = document.getElementById('kode_scan');
    const pesan = document.getElementById('scan-pesan');
    const reader = document.getElementById('reader');
    let kamera = null;

    async function cari(kode) {
        kode = (kode || '').trim();
        if (!kode) { return tampil('Kode gagal terbaca. Silakan pindai ulang.', true); }
        try {
            const res = await fetch(`{{ route('scan.cari') }}?kode=` + encodeURIComponent(kode), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!res.ok) { return tampil(data.message || 'Gagal memindai.', true); }
            tampil('Barang ditemukan: ' + data.nama, false);
            document.dispatchEvent(new CustomEvent('barang-ditemukan', { detail: data }));
        } catch (e) {
            tampil('Terjadi kesalahan jaringan.', true);
        }
        input.value = '';
        input.focus();
    }

    function tampil(teks, error) {
        pesan.textContent = teks;
        pesan.className = 'text-sm ' + (error ? 'text-red-600' : 'text-emerald-600');
        if (error) { document.dispatchEvent(new CustomEvent('barang-gagal', { detail: { pesan: teks } })); }
    }

    input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); cari(input.value); } });
    document.getElementById('btn-cari').addEventListener('click', () => cari(input.value));

    document.getElementById('btn-kamera').addEventListener('click', async function () {
        if (kamera) { await kamera.stop(); kamera = null; reader.classList.add('hidden'); this.textContent = 'Buka Kamera'; return; }
        reader.classList.remove('hidden');
        kamera = new Html5Qrcode('reader');
        try {
            await kamera.start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 }, async (teks) => {
                await kamera.stop(); kamera = null;
                reader.classList.add('hidden');
                document.getElementById('btn-kamera').textContent = 'Buka Kamera';
                cari(teks);
            });
            this.textContent = 'Tutup Kamera';
        } catch (e) {
            kamera = null; reader.classList.add('hidden');
            tampil('Kamera tidak dapat diakses. Gunakan HTTPS/localhost atau scanner USB.', true);
        }
    });
})();
</script>
@endpush

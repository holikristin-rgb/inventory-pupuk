<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Pupuk - Sistem Informasi Persediaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-slate-800">

    {{-- Navbar --}}
    <nav class="fixed top-0 z-50 w-full border-b border-emerald-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-2xl text-white">
                    🌱
                </div>
                <div>
                    <h1 class="text-lg font-bold text-emerald-800">Inventory Pupuk</h1>
                    <p class="text-xs text-slate-500">Warehouse Management System</p>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="rounded-lg px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                    Login
                </a>

                <a href="{{ route('register') }}"
                   class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Daftar
                </a>
            </div>

        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-green-100 pt-32">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-20 md:grid-cols-2">

            <div>
                <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700">
                    Sistem Informasi Manajemen Gudang
                </span>

                <h2 class="mt-6 text-4xl font-extrabold leading-tight text-slate-900 md:text-5xl">
                    Kelola Persediaan Pupuk dengan
                    <span class="text-emerald-600">Lebih Mudah</span>
                </h2>

                <p class="mt-6 max-w-xl text-base leading-8 text-slate-600">
                    Inventory Pupuk merupakan sistem informasi yang membantu
                    pengelolaan persediaan barang di gudang, mulai dari pencatatan
                    barang masuk dan keluar, pemantauan stok, hingga pengelolaan
                    lokasi penyimpanan secara terstruktur.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('login') }}"
                       class="rounded-xl bg-emerald-600 px-7 py-3.5 font-semibold text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700">
                        Mulai Sekarang →
                    </a>

                    <a href="#fitur"
                       class="rounded-xl border border-emerald-200 bg-white px-7 py-3.5 font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        Pelajari Fitur
                    </a>
                </div>
            </div>

            {{-- Ilustrasi --}}
            <div class="relative">
                <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-emerald-200 opacity-40 blur-3xl"></div>

                <div class="relative rounded-3xl border border-emerald-100 bg-white p-7 shadow-2xl shadow-emerald-100">
                    <div class="flex items-center justify-between border-b pb-5">
                        <div>
                            <h3 class="font-bold text-slate-800">Ringkasan Inventory</h3>
                            <p class="text-xs text-slate-500">Pengelolaan stok gudang</p>
                        </div>
                        <span class="rounded-lg bg-emerald-100 px-3 py-2 text-xl">📦</span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <p class="text-sm text-slate-500">Data Barang</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-700">01</p>
                            <p class="mt-1 text-xs text-slate-500">Pengelolaan barang</p>
                        </div>

                        <div class="rounded-2xl bg-blue-50 p-5">
                            <p class="text-sm text-slate-500">Barang Masuk</p>
                            <p class="mt-2 text-3xl font-bold text-blue-700">↗</p>
                            <p class="mt-1 text-xs text-slate-500">Pencatatan stok masuk</p>
                        </div>

                        <div class="rounded-2xl bg-orange-50 p-5">
                            <p class="text-sm text-slate-500">Barang Keluar</p>
                            <p class="mt-2 text-3xl font-bold text-orange-600">↙</p>
                            <p class="mt-1 text-xs text-slate-500">Pencatatan stok keluar</p>
                        </div>

                        <div class="rounded-2xl bg-purple-50 p-5">
                            <p class="text-sm text-slate-500">Monitoring</p>
                            <p class="mt-2 text-3xl font-bold text-purple-700">✓</p>
                            <p class="mt-1 text-xs text-slate-500">Pemantauan persediaan</p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl bg-slate-50 p-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🛡️</span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Kontrol Persediaan</p>
                                <p class="text-xs text-slate-500">Pemantauan Safety Stock gudang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Fitur --}}
    <section id="fitur" class="mx-auto max-w-7xl px-6 py-24">

        <div class="mx-auto max-w-2xl text-center">
            <span class="font-semibold text-emerald-600">FITUR SISTEM</span>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">
                Pengelolaan Gudang dalam Satu Sistem
            </h2>
            <p class="mt-4 leading-7 text-slate-500">
                Berbagai fitur untuk membantu proses pengelolaan dan pemantauan
                persediaan pupuk menjadi lebih terorganisir.
            </p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

            @php
                $fitur = [
                    ['📦', 'Manajemen Data Barang', 'Mengelola informasi barang, kategori, satuan, dan jumlah persediaan.'],
                    ['📥', 'Barang Masuk', 'Mencatat setiap penerimaan barang beserta informasi pemasok dan lokasi penyimpanan.'],
                    ['📤', 'Barang Keluar', 'Mencatat pengeluaran barang dan memantau perubahan jumlah stok.'],
                    ['🏬', 'Lokasi Penyimpanan', 'Mengatur lokasi penyimpanan berdasarkan lorong, rak, tingkat, dan slot.'],
                    ['🔔', 'Safety Stock', 'Memberikan peringatan ketika jumlah persediaan mencapai batas minimum.'],
                    ['📊', 'Laporan dan Monitoring', 'Memantau kondisi persediaan serta melihat riwayat aktivitas gudang.'],
                ];
            @endphp

            @foreach ($fitur as $item)
                <div class="rounded-2xl border border-slate-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-3xl">
                        {{ $item[0] }}
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-slate-800">
                        {{ $item[1] }}
                    </h3>

                    <p class="mt-3 text-sm leading-7 text-slate-500">
                        {{ $item[2] }}
                    </p>
                </div>
            @endforeach

        </div>
    </section>

    {{-- Alur Sistem --}}
    <section class="bg-emerald-50 py-20">
        <div class="mx-auto max-w-7xl px-6">

            <div class="text-center">
                <span class="font-semibold text-emerald-700">ALUR PENGELOLAAN</span>
                <h2 class="mt-3 text-3xl font-bold text-slate-900">
                    Proses Pengelolaan Persediaan
                </h2>
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-4">

                @foreach ([
                    ['01', 'Data Barang', 'Informasi barang dicatat ke dalam sistem.'],
                    ['02', 'Barang Masuk', 'Penerimaan barang dicatat sesuai jumlah dan lokasi.'],
                    ['03', 'Barang Keluar', 'Pengeluaran barang dicatat berdasarkan kebutuhan.'],
                    ['04', 'Monitoring Stok', 'Persediaan dipantau melalui laporan dan Safety Stock.'],
                ] as $alur)

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <span class="text-3xl font-extrabold text-emerald-200">{{ $alur[0] }}</span>
                        <h3 class="mt-4 font-bold text-slate-800">{{ $alur[1] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $alur[2] }}</p>
                    </div>

                @endforeach

            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="px-6 py-20">
        <div class="mx-auto max-w-5xl rounded-3xl bg-emerald-700 px-8 py-14 text-center text-white md:px-16">
            <h2 class="text-3xl font-bold">
                Mulai Kelola Persediaan Gudang
            </h2>

            <p class="mx-auto mt-4 max-w-2xl leading-7 text-emerald-100">
                Gunakan Inventory Pupuk untuk membantu proses pencatatan,
                pemantauan, dan pengelolaan persediaan gudang.
            </p>

            <a href="{{ route('login') }}"
               class="mt-8 inline-block rounded-xl bg-white px-8 py-3.5 font-bold text-emerald-700 transition hover:bg-emerald-50">
                Login ke Sistem →
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-950 px-6 py-8 text-center text-sm text-slate-400">
        <h3 class="font-bold text-white">Inventory Pupuk</h3>
        <p class="mt-2">Sistem Informasi Manajemen Persediaan Gudang Pupuk</p>
        <p class="mt-4">© {{ date('Y') }} Inventory Pupuk. All Rights Reserved.</p>
    </footer>

</body>
</html>
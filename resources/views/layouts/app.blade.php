<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Inventory Pupuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
        @layer components {
            .btn { @apply inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium transition; }
            .btn-primary { @apply btn bg-emerald-600 text-white hover:bg-emerald-700; }
            .btn-secondary { @apply btn bg-slate-200 text-slate-800 hover:bg-slate-300; }
            .btn-danger { @apply btn bg-red-600 text-white hover:bg-red-700; }
            .input { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500; }
            .label { @apply mb-1 block text-sm font-medium text-slate-700; }
            .card { @apply rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200; }
            .th { @apply bg-slate-100 px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600; }
            .td { @apply border-t border-slate-100 px-3 py-2 text-sm text-slate-700; }
            .err { @apply mt-1 text-xs text-red-600; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
@php
    $u = auth()->user();

    $menu = [
        ['dashboard', 'Dashboard', null],
        ['monitoring.index', 'Monitoring Stok', null],
        ['rak.index', 'Visualisasi Lokasi Rak', null],
        ['barang.index', 'Data Barang', null],
        ['lokasi.index', 'Lokasi Penyimpanan', ['staf_gudang', 'kepala_gudang']],
        ['barang-masuk.index', 'Barang Masuk', null],
        ['barang-keluar.index', 'Barang Keluar', null],
        ['scan.index', 'Pemindaian Barcode/QR', ['staf_gudang']],
        ['opname.index', 'Stock Opname', null],
        ['safety-stock.index', 'Peringatan Safety Stock', ['kepala_gudang']],
        ['laporan.index', 'Laporan Inventory', ['kepala_gudang', 'auditor']],
        ['log.index', 'Riwayat Aktivitas', ['kepala_gudang', 'auditor']],
    ];
@endphp
        <nav class="space-y-1">
            @foreach ($menu as [$rute, $teks, $peran])
                @if (is_null($peran) || $u->hasRole(...$peran))
                    <a href="{{ route($rute) }}"
                       class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs($rute === 'dashboard' ? 'dashboard' : \Illuminate\Support\Str::before($rute, '.') . '.*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800' }}">
                        {{ $teks }}
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Topbar --}}
        <header class="flex items-center justify-between border-b bg-white px-6 py-3">
            <h1 class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>
            <div class="flex items-center gap-4">
                @if ($u->hasRole('kepala_gudang'))
                    @php($jumlahNotif = $u->unreadNotifications()->count())
                    <a href="{{ route('safety-stock.index') }}" class="relative rounded-full p-2 hover:bg-slate-100" title="Peringatan Safety Stock">
                        <span class="text-xl">🔔</span>
                        @if ($jumlahNotif > 0)
                            <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 text-xs font-bold text-white">{{ $jumlahNotif }}</span>
                        @endif
                    </a>
                @endif
                <div class="text-right text-sm leading-tight">
                    <div class="font-medium">{{ $u->name }}</div>
                    <div class="text-xs text-slate-500">{{ $u->role_label }}</div>
                </div>
                {{-- Tombol Logout --}}
                <button type="button" onclick="bukaModalLogout()"
                    class="rounded-lg bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                    Logout
                </button>

                {{-- Form Logout --}}
                <form id="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </header>

        <main class="flex-1 p-6">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>

{{-- Modal Konfirmasi Logout --}}
<div id="modalLogout"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
            <svg class="h-8 w-8 text-red-600" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </div>

        <h2 class="text-center text-xl font-bold text-slate-800">
            Konfirmasi Logout
        </h2>

        <p class="mt-2 text-center text-sm leading-relaxed text-slate-500">
            Apakah Anda yakin ingin keluar dari sistem Inventory Pupuk?
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <button type="button" onclick="tutupModalLogout()"
                class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                Batal
            </button>

            <button type="button" onclick="konfirmasiLogout()"
                class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                Ya, Logout
            </button>
        </div>

    </div>
</div>

<script>
    function bukaModalLogout() {
        const modal = document.getElementById('modalLogout');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupModalLogout() {
        const modal = document.getElementById('modalLogout');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function konfirmasiLogout() {
        document.getElementById('logoutForm').submit();
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupModalLogout();
        }
    });
</script>
@stack('scripts')
</body>
</html>

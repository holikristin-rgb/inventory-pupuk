<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Inventory Pupuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100">
    <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow">
        <h1 class="text-center text-xl font-bold text-slate-800">Sistem Informasi Manajemen Inventory</h1>
        <p class="mb-6 mt-1 text-center text-sm text-slate-500">dengan Peringatan Safety Stock</p>

        @if (session('sukses'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('sukses') }}</div>
        @endif

        <form method="POST" action="{{ route('login.proses') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>

                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        placeholder="Masukkan password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    >

                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-emerald-600"
                        aria-label="Lihat password"
                    >
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>

                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                            class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.25 0 9.75 7 9.75 7a15.5 15.5 0 01-3.1 3.8M6.2 6.2C3.7 7.8 2.25 12 2.25 12s3.5 7 9.75 7c1 0 2-.2 2.9-.5"/>
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded"> Ingat saya
            </label>
            <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">Masuk</button>
        </form>

        <p class="mt-5 text-center text-xs text-slate-500">
            Kepala Gudang / Auditor belum punya akun? <a href="{{ route('register') }}" class="font-medium text-emerald-700 hover:underline">Daftar</a>
            <br>Akun Staf Gudang disediakan oleh sistem.
        </p>
    </div>
    <script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';

        passwordInput.type = isHidden ? 'text' : 'password';

        eyeOpen.classList.toggle('hidden', isHidden);
        eyeClosed.classList.toggle('hidden', !isHidden);

        togglePassword.setAttribute(
            'aria-label',
            isHidden ? 'Sembunyikan password' : 'Lihat password'
        );
    });
</script>
</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Akun - Inventory Pupuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen items-center justify-center bg-slate-100 py-8">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow">

        <h1 class="text-center text-xl font-bold text-slate-800">
            Registrasi Akun
        </h1>

        <p class="mb-6 mt-1 text-center text-sm text-slate-500">
            Khusus Kepala Gudang dan Auditor
        </p>

        <form method="POST" action="{{ route('register.proses') }}" class="space-y-4">
            @csrf

            @php($in = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500')

            <!-- Nama Lengkap -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="{{ $in }}"
                >

                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username dan Role -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        class="{{ $in }}"
                    >

                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Role
                    </label>

                    <select name="role" required class="{{ $in }}">
                        <option value="kepala_gudang" @selected(old('role') === 'kepala_gudang')>
                            Kepala Gudang
                        </option>

                        <option value="auditor" @selected(old('role') === 'auditor')>
                            Auditor
                        </option>
                    </select>

                    @error('role')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="{{ $in }}"
                >

                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password dan Ulangi Password -->
            <div class="grid grid-cols-2 gap-3">

                <!-- Password -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Password
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            class="{{ $in }} pr-10"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password', 'eye1', 'eyeOff1')"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-emerald-600"
                            aria-label="Lihat password"
                        >
                            <!-- Ikon mata terbuka -->
                            <svg
                                id="eye1"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z"
                                />
                                <circle cx="12" cy="12" r="3"/>
                            </svg>

                            <!-- Ikon mata tertutup -->
                            <svg
                                id="eyeOff1"
                                xmlns="http://www.w3.org/2000/svg"
                                class="hidden h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.25 0 9.75 7 9.75 7a15.5 15.5 0 01-3.1 3.8M6.2 6.2C3.7 7.8 2.25 12 2.25 12s3.5 7 9.75 7c1 0 2-.2 2.9-.5"
                                />
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ulangi Password -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Ulangi Password
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            class="{{ $in }} pr-10"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', 'eye2', 'eyeOff2')"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-emerald-600"
                            aria-label="Lihat ulangi password"
                        >
                            <!-- Ikon mata terbuka -->
                            <svg
                                id="eye2"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z"
                                />
                                <circle cx="12" cy="12" r="3"/>
                            </svg>

                            <!-- Ikon mata tertutup -->
                            <svg
                                id="eyeOff2"
                                xmlns="http://www.w3.org/2000/svg"
                                class="hidden h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6.25 0 9.75 7 9.75 7a15.5 15.5 0 01-3.1 3.8M6.2 6.2C3.7 7.8 2.25 12 2.25 12s3.5 7 9.75 7c1 0 2-.2 2.9-.5"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Kode Registrasi -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">
                    Kode Registrasi
                </label>

                <input
                    type="password"
                    name="kode_registrasi"
                    required
                    class="{{ $in }}"
                    placeholder="Diberikan oleh pengelola sistem"
                >

                @error('kode_registrasi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Daftar -->
            <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
                Daftar
            </button>

        </form>

        <p class="mt-5 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-emerald-700 hover:underline">
                Login
            </a>
        </p>

    </div>

    <!-- JavaScript Lihat Password -->
    <script>
        function togglePassword(inputId, eyeId, eyeOffId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeOff = document.getElementById(eyeOffId);

            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';

            eye.classList.toggle('hidden', isHidden);
            eyeOff.classList.toggle('hidden', !isHidden);
        }
    </script>

</body>
</html>

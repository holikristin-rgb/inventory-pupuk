<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $kredensial = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Staf Gudang hanya boleh login memakai akun yang sudah ditentukan (config/akses.php).
        $calon = User::where('username', $kredensial['username'])->first();
        if ($calon && $calon->hasRole(User::STAF)
            && ! array_key_exists($calon->username, config('akses.staf'))) {
            return back()
                ->withErrors(['username' => 'Akun staf ini tidak terdaftar sebagai akun yang diizinkan.'])
                ->onlyInput('username');
        }

        if (Auth::attempt($kredensial, $request->boolean('remember'))) {
            $request->session()->regenerate();
            LogAktivitas::catat('login', 'auth', 'Login ke sistem');

            // Dashboard ditampilkan sesuai hak akses pengguna.
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->onlyInput('username');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /** Pendaftaran akun khusus Kepala Gudang dan Auditor. Staf tidak bisa mendaftar. */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'username'      => ['required', 'alpha_dash', 'min:4', 'max:30', 'unique:users,username'],
            'email'         => ['required', 'email', 'max:150', 'unique:users,email'],
            'role'          => ['required', Rule::in(config('akses.role_registrasi'))],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'kode_registrasi' => ['required', 'string'],
        ]);

        if (! hash_equals((string) config('akses.kode_registrasi'), $data['kode_registrasi'])) {
            return back()
                ->withErrors(['kode_registrasi' => 'Kode registrasi tidak valid.'])
                ->onlyInput('name', 'username', 'email', 'role');
        }

        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => $data['password'],
        ]);

        LogAktivitas::catat('tambah', 'akun', "Registrasi akun {$user->username} sebagai {$user->role_label}", $user->id);

        return redirect()->route('login')->with('sukses', 'Akun berhasil didaftarkan. Silakan login.');
    }

    public function logout(Request $request)
    {
        LogAktivitas::catat('logout', 'auth', 'Logout dari sistem');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

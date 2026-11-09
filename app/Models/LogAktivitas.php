<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'aksi', 'modul', 'deskripsi', 'ip_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Pencatatan otomatis jejak aktivitas pengguna. */
    public static function catat(string $aksi, string $modul, string $deskripsi, ?int $userId = null): self
    {
        return static::create([
            'user_id'    => $userId ?? Auth::id(),
            'aksi'       => $aksi,
            'modul'      => $modul,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip(),
        ]);
    }
}

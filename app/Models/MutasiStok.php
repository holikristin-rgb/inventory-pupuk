<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiStok extends Model
{
    protected $table = 'mutasi_stok';

    protected $fillable = [
        'kode_transaksi', 'barang_id', 'lokasi_id', 'user_id', 'jenis', 'jumlah',
        'stok_sebelum', 'stok_sesudah', 'pihak', 'keterangan', 'rincian_lokasi',
    ];

    protected $casts = [
        'rincian_lokasi' => 'array',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(LokasiRak::class, 'lokasi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

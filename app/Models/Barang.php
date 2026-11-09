<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode_barang', 'nama', 'jenis', 'satuan', 'stok',
        'pemakaian_rata_rata', 'pemakaian_maksimum', 'lead_time',
        'safety_stock', 'keterangan',
    ];

    protected $casts = [
        'pemakaian_rata_rata' => 'float',
        'pemakaian_maksimum'  => 'float',
    ];

    protected static function booted(): void
    {
        // Safety Stock dihitung ulang otomatis setiap barang disimpan.
        static::saving(function (Barang $barang) {
            $barang->safety_stock = $barang->hitungSafetyStock();
        });
    }

    /**
     * Safety Stock = (pemakaian maksimum - pemakaian rata-rata) x lead time.
     * Ubah rumus di sini bila laporan menggunakan rumus lain.
     */
    public function hitungSafetyStock(): int
    {
        $selisih = max(0, $this->pemakaian_maksimum - $this->pemakaian_rata_rata);

        return (int) ceil($selisih * $this->lead_time);
    }

    /** Reorder Point = (pemakaian rata-rata x lead time) + Safety Stock. */
    public function getReorderPointAttribute(): int
    {
        return (int) ceil($this->pemakaian_rata_rata * $this->lead_time) + $this->safety_stock;
    }

    /** Saran jumlah pengadaan ulang agar stok kembali di atas reorder point. */
    public function getSaranPengadaanAttribute(): int
    {
        return max(0, $this->reorder_point - $this->stok);
    }

    /** habis | menipis | aman */
    public function getStatusAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'habis';
        }

        return $this->stok <= $this->safety_stock ? 'menipis' : 'aman';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'habis'   => 'Habis',
            'menipis' => 'Di Bawah Safety Stock',
            default   => 'Aman',
        };
    }

    public function scopeMenipis(Builder $query): Builder
    {
        return $query->whereColumn('stok', '<=', 'safety_stock');
    }

    public function lokasi(): HasMany
    {
        return $this->hasMany(LokasiRak::class);
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }

    public function opnameDetail(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class);
    }
}

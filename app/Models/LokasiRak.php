<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LokasiRak extends Model
{
    protected $table = 'lokasi_rak';

    protected $fillable = [
        'lorong', 'rak', 'tingkat', 'slot', 'kapasitas',
        'barang_id', 'jumlah', 'tanggal_penerimaan',
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function getKodeAttribute(): string
    {
        return "{$this->lorong}-R{$this->rak}-T{$this->tingkat}-S{$this->slot}";
    }

    public function getLabelAttribute(): string
    {
        return "Lorong {$this->lorong} / Rak {$this->rak} / Tingkat {$this->tingkat} / Slot {$this->slot}";
    }

    public function getKosongAttribute(): bool
    {
        return $this->jumlah <= 0;
    }
}

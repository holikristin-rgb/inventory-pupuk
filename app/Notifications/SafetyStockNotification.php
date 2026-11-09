<?php

namespace App\Notifications;

use App\Models\Barang;
use Illuminate\Notifications\Notification;

class SafetyStockNotification extends Notification
{
    public function __construct(public Barang $barang)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'barang_id'    => $this->barang->id,
            'kode_barang'  => $this->barang->kode_barang,
            'nama'         => $this->barang->nama,
            'stok'         => $this->barang->stok,
            'safety_stock' => $this->barang->safety_stock,
            'pesan'        => "Stok {$this->barang->nama} tersisa {$this->barang->stok} {$this->barang->satuan}, "
                            . "mencapai/di bawah batas Safety Stock ({$this->barang->safety_stock}). Segera lakukan pengadaan ulang.",
        ];
    }
}

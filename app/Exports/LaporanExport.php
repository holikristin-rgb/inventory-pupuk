<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $kolom, private array $baris)
    {
    }

    public function array(): array
    {
        return $this->baris;
    }

    public function headings(): array
    {
        return $this->kolom;
    }
}

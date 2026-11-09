<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();          // isi Barcode / QR Code
            $table->string('nama');
            $table->string('jenis')->nullable();              // mis. Urea, NPK, ZA
            $table->string('satuan', 20)->default('karung');
            $table->unsignedInteger('stok')->default(0);
            // parameter perhitungan Safety Stock
            $table->decimal('pemakaian_rata_rata', 10, 2)->default(0); // per hari
            $table->decimal('pemakaian_maksimum', 10, 2)->default(0);  // per hari
            $table->unsignedInteger('lead_time')->default(0);          // hari
            $table->unsignedInteger('safety_stock')->default(0);       // hasil perhitungan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

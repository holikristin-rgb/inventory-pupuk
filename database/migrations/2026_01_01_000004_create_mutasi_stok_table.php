<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('barang_id')->constrained('barang')->restrictOnDelete();
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi_rak')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->unsignedInteger('jumlah');
            $table->unsignedInteger('stok_sebelum');
            $table->unsignedInteger('stok_sesudah');
            $table->string('pihak')->nullable();          // pemasok (masuk) / tujuan (keluar)
            $table->text('keterangan')->nullable();
            $table->json('rincian_lokasi')->nullable();   // slot yang dikurangi pada barang keluar
            $table->timestamps();

            $table->index(['jenis', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
    }
};

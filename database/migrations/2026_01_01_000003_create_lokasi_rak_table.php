<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_rak', function (Blueprint $table) {
            $table->id();
            $table->string('lorong', 10);
            $table->unsignedSmallInteger('rak');
            $table->unsignedSmallInteger('tingkat');
            $table->unsignedSmallInteger('slot');
            $table->unsignedInteger('kapasitas')->default(100);
            $table->foreignId('barang_id')->nullable()->constrained('barang')->nullOnDelete();
            $table->unsignedInteger('jumlah')->default(0);
            $table->dateTime('tanggal_penerimaan')->nullable();
            $table->timestamps();

            $table->unique(['lorong', 'rak', 'tingkat', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_rak');
    }
};

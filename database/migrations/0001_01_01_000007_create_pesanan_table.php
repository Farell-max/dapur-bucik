<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('meja_id')->nullable()->constrained('meja')->nullOnDelete();
            $table->enum('metode_penerimaan', ['dine-in', 'pickup', 'kurir_mandiri']);
            $table->enum('metode_pembayaran', ['transfer_bank', 'qris', 'bayar_di_tempat']);
            $table->enum('status', [
                'menunggu_konfirmasi',
                'dimasak',
                'siap_diambil',
                'menunggu_driver',
                'selesai',
                'ditolak',
            ])->default('menunggu_konfirmasi');
            $table->string('alasan_tolak')->nullable();
            $table->decimal('total_harga', 12, 2);
            $table->dateTime('estimasi_waktu_ambil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail (histori aktivitas) transaksi — readonly, ditampilkan sbg timeline
     * di modal Detail Transaksi. `type` sengaja string bebas (bukan DB enum) + `metadata`
     * json supaya jenis aktivitas baru gampang ditambah tanpa migration baru.
     */
    public function up(): void
    {
        Schema::create('transaksi_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->cascadeOnDelete();

            // Contoh: created, opened, note_updated, proof_uploaded, verified, rejected, status_changed
            $table->string('type', 50);

            // Teks human-readable bahasa Indonesia, siap tampil langsung di timeline.
            $table->string('description');

            // Null = aktivitas otomatis oleh sistem (mis. transaksi dibuat saat pendaftaran).
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();

            // Detail tambahan bebas (mis. status_from/status_to) — extensible utk aktivitas baru.
            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('transaksi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_activities');
    }
};

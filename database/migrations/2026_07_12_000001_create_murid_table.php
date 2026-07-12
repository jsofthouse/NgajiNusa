<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email');
            $table->string('whatsapp', 20);
            $table->string('level_belajar', 50);
            $table->string('paket', 50);
            $table->string('status', 30)->default('Daftar');
            // status lain menyusul di fase berikutnya: Aktif, Pending, Nonaktif, dst
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
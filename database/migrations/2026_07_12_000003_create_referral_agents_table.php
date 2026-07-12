<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_agents', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('whatsapp', 20);
            $table->string('kode', 50)->unique();
            $table->string('status', 20)->default('Aktif');
            // status: Aktif / Nonaktif
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_agents');
    }
};

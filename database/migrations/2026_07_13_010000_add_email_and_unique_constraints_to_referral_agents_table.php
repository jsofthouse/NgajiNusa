<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            // Disiapkan untuk fitur login Agent di masa depan (belum diimplementasikan).
            $table->string('email', 150)->unique()->after('nama');
            $table->unique('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            $table->dropUnique(['whatsapp']);
            $table->dropColumn('email');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            // Modul admin Murid: hapus data pakai soft delete, belum ada halaman Trash/Restore.
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

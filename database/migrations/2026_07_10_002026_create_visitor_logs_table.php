<?php
// database/migrations/xxxx_xx_xx_create_visitor_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date')->index();
            $table->string('path', 255);
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('hit_count')->default(1);
            $table->timestamps();

            $table->unique(['visit_date', 'path', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};

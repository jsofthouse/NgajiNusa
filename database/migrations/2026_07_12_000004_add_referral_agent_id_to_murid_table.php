<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->foreignId('referral_agent_id')
                ->nullable()
                ->after('status')
                ->constrained('referral_agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('murid', function (Blueprint $table) {
            $table->dropForeign(['referral_agent_id']);
            $table->dropColumn('referral_agent_id');
        });
    }
};

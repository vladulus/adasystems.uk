<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('pending_command')->nullable()->after('upload_interval');
            $table->json('dtc_codes')->nullable()->after('pending_command');
            $table->timestamp('dtc_updated_at')->nullable()->after('dtc_codes');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['pending_command', 'dtc_codes', 'dtc_updated_at']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_telemetry', function (Blueprint $table) {
            // OBD extra fields
            $table->decimal('intake_temp', 5, 2)->nullable()->after('engine_load');
            $table->decimal('voltage', 5, 2)->nullable()->after('intake_temp');
            
            // OBD Diesel fields
            $table->decimal('boost_pressure', 8, 2)->nullable()->after('voltage');
            $table->decimal('rail_pressure', 8, 2)->nullable()->after('boost_pressure');
            $table->decimal('egr', 5, 2)->nullable()->after('rail_pressure');
            $table->decimal('dpf_temp_in', 6, 2)->nullable()->after('egr');
            $table->decimal('dpf_temp_out', 6, 2)->nullable()->after('dpf_temp_in');
            $table->decimal('dpf_soot', 5, 2)->nullable()->after('dpf_temp_out');
            
            // Modem data usage
            $table->decimal('data_used', 10, 2)->nullable()->after('operator');
        });
    }

    public function down(): void
    {
        Schema::table('device_telemetry', function (Blueprint $table) {
            $table->dropColumn([
                'intake_temp',
                'voltage',
                'boost_pressure',
                'rail_pressure',
                'egr',
                'dpf_temp_in',
                'dpf_temp_out',
                'dpf_soot',
                'data_used'
            ]);
        });
    }
};
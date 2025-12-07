<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_telemetry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->timestamp('recorded_at');
            
            // GPS
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('speed', 6, 2)->nullable();         // km/h
            $table->decimal('heading', 5, 2)->nullable();       // degrees
            $table->integer('satellites')->nullable();
            
            // OBD
            $table->integer('rpm')->nullable();
            $table->integer('vehicle_speed')->nullable();       // km/h from OBD
            $table->decimal('coolant_temp', 5, 2)->nullable();  // °C
            $table->decimal('fuel_level', 5, 2)->nullable();    // %
            $table->decimal('throttle', 5, 2)->nullable();      // %
            $table->decimal('engine_load', 5, 2)->nullable();   // %
            
            // Modem
            $table->integer('signal_strength')->nullable();     // dBm
            $table->string('network_type', 20)->nullable();     // 4G, 3G, etc
            $table->string('operator', 50)->nullable();
            
            // UPS
            $table->decimal('battery_percent', 5, 2)->nullable();
            $table->decimal('battery_voltage', 5, 3)->nullable();
            $table->boolean('is_charging')->nullable();
            
            // System
            $table->decimal('cpu_temp', 5, 2)->nullable();
            $table->decimal('cpu_usage', 5, 2)->nullable();
            $table->decimal('memory_usage', 5, 2)->nullable();
            $table->decimal('disk_usage', 5, 2)->nullable();
            
            // Raw snapshot pentru extra data
            $table->json('raw_data')->nullable();
            
            $table->timestamps();
            
            // Indexuri pentru queries rapide
            $table->index(['device_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_telemetry');
    }
};

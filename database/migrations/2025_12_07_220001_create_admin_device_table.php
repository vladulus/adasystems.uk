<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot table: Admin ↔ Device (many-to-many)
     * Un admin poate administra mai multe device-uri
     * Un device poate fi administrat de mai mulți admini
     */
    public function up(): void
    {
        Schema::create('admin_device', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->timestamps();
            
            // Un admin nu poate fi alocat de 2 ori aceluiași device
            $table->unique(['admin_id', 'device_id']);
            
            // Index pentru căutări rapide
            $table->index('admin_id');
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_device');
    }
};

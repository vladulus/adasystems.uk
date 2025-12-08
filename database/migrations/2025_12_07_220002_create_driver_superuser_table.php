<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot table: Driver ↔ Superuser (many-to-many)
     * Un driver poate lucra pentru mai mulți superuseri (employers)
     * Un superuser poate avea mai mulți driveri angajați
     */
    public function up(): void
    {
        Schema::create('driver_superuser', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('superuser_id')->constrained('users')->onDelete('cascade');
            $table->date('hired_from')->nullable()->comment('Data de la care lucrează');
            $table->date('hired_to')->nullable()->comment('Data până la care lucrează (null = activ)');
            $table->boolean('is_active')->default(true)->comment('Angajare activă');
            $table->timestamps();
            
            // Un driver nu poate fi angajat de 2 ori de același superuser în aceeași perioadă
            $table->unique(['driver_id', 'superuser_id', 'hired_from']);
            
            // Index pentru căutări rapide
            $table->index('driver_id');
            $table->index('superuser_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_superuser');
    }
};

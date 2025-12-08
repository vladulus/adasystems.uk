<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot table: Admin ↔ Superuser (many-to-many)
     * Un admin poate gestiona mai mulți superuseri (clienți)
     * Un superuser poate fi gestionat de mai mulți admini
     */
    public function up(): void
    {
        Schema::create('admin_superuser', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('superuser_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Un admin nu poate fi alocat de 2 ori aceluiași superuser
            $table->unique(['admin_id', 'superuser_id']);
            
            // Index pentru căutări rapide
            $table->index('admin_id');
            $table->index('superuser_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_superuser');
    }
};

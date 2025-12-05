<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverVehicleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_vehicle', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('vehicle_id')
                ->constrained()
                ->cascadeOnDelete();

            // câmpuri extra – le poți lăsa sau scoate
            $table->date('assigned_from')->nullable();
            $table->date('assigned_to')->nullable();
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // prevenim duplicatele exacte
            $table->unique(['driver_id', 'vehicle_id', 'assigned_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_vehicle');
    }
}

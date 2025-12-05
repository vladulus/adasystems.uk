<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            // Personal Info
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();

            // License Info
            $table->string('license_number');
            $table->string('license_type');
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date');

            // Employment Info
            $table->string('status')->default('active');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
};

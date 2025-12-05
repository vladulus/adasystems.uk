<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adaugă DOAR coloanele care lipsesc
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('department')->nullable()->after('phone');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('last_login');
        });

        // Sync is_active to status (status există deja)
        DB::table('users')->where('is_active', true)->update(['status' => 'active']);
        DB::table('users')->where('is_active', false)->update(['status' => 'inactive']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'department', 'created_by', 'last_login_at']);
        });
    }
};
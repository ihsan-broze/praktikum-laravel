<?php

// database/migrations/2025_06_09_099999_add_role_and_qr_to_users_table.php
// FIXED VERSION - Include moderator in enum OR use string

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // OPTION 1: Use string instead of enum (RECOMMENDED) $table->string('role')->default('user');
            
            // OPTION 2: Include moderator in enum
            $table->enum('role', ['user', 'moderator', 'admin'])->default('user')->after('email_verified_at');
            
            $table->string('profile_qr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_qr']);
        });
    }
};
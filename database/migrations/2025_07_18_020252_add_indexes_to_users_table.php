<?php

// File: database/migrations/2024_01_15_000001_add_indexes_to_users_table.php
// Nama file migration menggunakan format: YYYY_MM_DD_HHMMSS_add_indexes_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Index untuk kolom yang sering di-query berdasarkan User model
            $table->index('email');
            $table->index('role');
            $table->index('email_verified_at');
            $table->index(['role', 'created_at']);
            
            // Composite index untuk query kombinasi
            $table->index(['email', 'role']);
            $table->index(['role', 'email_verified_at']);
            
            // Index untuk soft deletes
            $table->index('deleted_at');
            
            // Index untuk kombinasi dengan soft deletes
            $table->index(['role', 'deleted_at']);
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['role', 'created_at']);
            $table->dropIndex(['email', 'role']);
            $table->dropIndex(['role', 'email_verified_at']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['role', 'deleted_at']);
        });
    }
}

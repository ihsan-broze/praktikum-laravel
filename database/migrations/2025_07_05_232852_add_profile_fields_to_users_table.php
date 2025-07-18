<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add title column if it doesn't exist
            if (!Schema::hasColumn('categories', 'title')) {
                $table->string('title')->after('name');
            }
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
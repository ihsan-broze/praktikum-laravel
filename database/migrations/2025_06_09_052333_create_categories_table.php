<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable()->change();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // Add this
            $table->boolean('is_active')->default(true); // Add this
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
    
};
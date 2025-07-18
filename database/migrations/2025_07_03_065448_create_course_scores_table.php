<?php
// Migration: create_course_scores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score')->comment('Score 1-100');
            $table->text('review')->nullable();
            $table->enum('difficulty_rating', ['easy', 'medium', 'hard'])->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['course_id', 'user_id']);
            $table->index(['course_id', 'score']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_scores');
    }
};
<?php
// app/Models/CourseScore.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseScore extends Model
{
    protected $fillable = [
        'course_id',
        'user_id', 
        'score',
        'review',
        'difficulty_rating',
        'completion_percentage',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
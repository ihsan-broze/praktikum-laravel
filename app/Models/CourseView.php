<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseView extends Model
{
    protected $fillable = [
        'user_id',
        'course_id'
        // Hapus 'viewed_at' jika kolom tidak ada
    ];
    
    // Hapus casting viewed_at jika tidak menggunakan kolom tersebut
    // protected $casts = [
    //     'viewed_at' => 'datetime',
    // ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}   
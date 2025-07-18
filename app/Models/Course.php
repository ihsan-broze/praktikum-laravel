<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'image',
        'category_id',
        'created_by',
        'duration',
        'level',
        'status',
        'featured',
    ];

    // Perbaiki relasi dengan eksplisit foreign key dan local key
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
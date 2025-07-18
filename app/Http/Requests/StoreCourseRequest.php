<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Pastikan ini true
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'duration' => 'nullable|integer|min:1',
            'content' => 'nullable|string',
            'status' => 'nullable|in:draft,published,archived',
            'featured' => 'nullable|boolean',
        ];
    }
}
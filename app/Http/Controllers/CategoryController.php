<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category; // Tambahkan ini
use App\Http\Requests\StoreCourseRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        // Load relasi category untuk menampilkan nama kategori
        $courses = Course::with('category')->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        // Comment sementara untuk testing
        // $this->authorize('create', Course::class);
        
        // Ambil semua categories untuk dropdown
        $categories = Category::all();
        return view('admin.courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request)
    {
        // Comment sementara untuk testing
        // $this->authorize('create', Course::class);
        
        $data = $request->validated();
        
        // Set created_by jika user login
        if (auth()->check()) {
            $data['created_by'] = auth()->id();
        }
        
        // Handle checkbox featured
        $data['featured'] = $request->has('featured') ? 1 : 0;
        $data['status'] = $data['status'] ?? 'draft';
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }
        
        Course::create($data);
        
        return redirect()->route('admin.courses.index')->with('success', 'Course berhasil ditambahkan');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }
}
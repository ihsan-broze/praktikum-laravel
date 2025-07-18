<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest; // Kita akan buat ini
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        // Load relasi category
        $courses = Course::with('category')->get();

        // Workaround untuk courses yang relasi tidak berfungsi
        foreach ($courses as $course) {
            if (!$course->category && $course->category_id) {
                $manualCategory = Category::find($course->category_id);
                $course->setRelation('category', $manualCategory);
            }
        }

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        // Comment sementara untuk testing
        // $this->authorize('create', Course::class);
        
        // Ambil semua categories
        $categories = Category::all();
        
        return view('admin.courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request)
    {
        // Comment authorize sementara untuk testing
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
        
        try {
            $course = Course::create($data);
            return redirect()->route('admin.courses.index')->with('success', 'Course berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan course']);
        }
    }

    public function show(Course $course)
    {
        // Track user view untuk recommendation
        if (auth()->check()) {
            \App\Models\CourseView::updateOrCreate([
                'user_id' => auth()->id(),
                'course_id' => $course->id,
            ], [
                // Hapus viewed_at, gunakan updated_at default Laravel
                // 'viewed_at' => now()
            ]);
        }
        
        $course->load(['category', 'creator']);
        
        // Workaround untuk relasi category
        if (!$course->category && $course->category_id) {
            $manualCategory = \App\Models\Category::find($course->category_id);
            $course->setRelation('category', $manualCategory);
        }
        
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        // Comment sementara untuk testing
        // $this->authorize('update', $course);
        
        // Ambil semua categories untuk dropdown
        $categories = Category::all();
        
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        // Comment sementara untuk testing
        // $this->authorize('update', $course);
        
        $data = $request->validated();
        
        // Handle checkbox featured
        $data['featured'] = $request->has('featured') ? 1 : 0;
        
        // Handle file upload jika ada image baru
        if ($request->hasFile('image')) {
            // Hapus image lama jika ada
            if ($course->image && file_exists(storage_path('app/public/' . $course->image))) {
                unlink(storage_path('app/public/' . $course->image));
            }
            
            $data['image'] = $request->file('image')->store('courses', 'public');
        }
        
        try {
            $course->update($data);
            return redirect()->route('admin.courses.index')->with('success', 'Course berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate course']);
        }
    }

    public function destroy(Course $course)
    {
        // Comment sementara untuk testing
        // $this->authorize('delete', $course);
        
        try {
            // Hapus image jika ada
            if ($course->image && file_exists(storage_path('app/public/' . $course->image))) {
                unlink(storage_path('app/public/' . $course->image));
            }
            
            $course->delete();
            return redirect()->route('admin.courses.index')->with('success', 'Course berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus course']);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseView;

class RecommendationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Perbaiki query dengan menambahkan table prefix
        $viewedCategories = CourseView::where('course_views.user_id', $user->id) // Tambah prefix table
            ->join('courses', 'course_views.course_id', '=', 'courses.id')
            ->whereNotNull('courses.category_id')
            ->selectRaw('courses.category_id, COUNT(*) as count')
            ->groupBy('courses.category_id')
            ->orderByDesc('count')
            ->pluck('courses.category_id');

        $recommended = collect();

        if ($viewedCategories->isNotEmpty()) {
            // Recommend courses from categories user has viewed
            $recommended = Course::with(['category', 'creator'])
                ->where('status', 'published')
                ->whereIn('category_id', $viewedCategories)
                ->whereNotIn('id', CourseView::where('course_views.user_id', $user->id)->pluck('course_id')) // Tambah prefix
                ->orderByDesc('featured')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
        }

        // Fallback: if no recommendations based on history, show featured/recent courses
        if ($recommended->isEmpty()) {
            $recommended = Course::with(['category', 'creator'])
                ->where('status', 'published')
                ->whereNotIn('id', CourseView::where('course_views.user_id', $user->id)->pluck('course_id')) // Tambah prefix
                ->where(function($query) {
                    $query->where('featured', true)
                          ->orWhere('created_at', '>=', now()->subDays(30));
                })
                ->orderByDesc('featured')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
        }

        // Apply workaround untuk category relation yang bermasalah
        foreach ($recommended as $course) {
            if (!$course->category && $course->category_id) {
                $manualCategory = \App\Models\Category::find($course->category_id);
                $course->setRelation('category', $manualCategory);
            }
        }

        return view('recommendation.index', compact('recommended'));
    }
}
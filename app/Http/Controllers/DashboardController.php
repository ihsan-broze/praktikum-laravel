<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show dashboard - handles both admin and user based on role
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Redirect admin users to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        // User-specific data
        $enrolledCourses = 0;
        $completedCourses = 0;
        
        // Check if user has courses relationship
        if (method_exists($user, 'courses')) {
            $enrolledCourses = $user->courses()->count();
            $completedCourses = $user->courses()->where('status', 'completed')->count();
        }
        
        // General statistics for user to see
        $totalCourses = Course::count();
        $categories = [];
        
        // Check if Course model exists and has category field
        if (class_exists('\App\Models\Course')) {
            try {
                $categories = Course::select('category', DB::raw('count(*) as total'))
                    ->whereNotNull('category')
                    ->groupBy('category')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                // If category column doesn't exist, use empty array
                $categories = collect([]);
            }
        }

        return view('dashboard', compact(
            'enrolledCourses',
            'completedCourses',
            'totalCourses',
            'categories'
        ));
    }
    
    /**
     * Show user-specific dashboard (alternative method)
     */
    public function userDashboard(): View
    {
        return $this->index();
    }
}
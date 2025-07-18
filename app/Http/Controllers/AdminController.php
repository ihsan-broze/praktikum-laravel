<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard with comprehensive statistics
     */
    public function dashboard(): View
    {
        // User statistics
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $regularUsers = User::where('role', 'user')->count();
        
        // Course statistics
        $totalCourses = Course::count();
        $categories = Course::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')->get();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'adminUsers', 
            'regularUsers', 
            'totalCourses', 
            'categories'
        ));
    }

    /**
     * Show users list
     */
    public function users(): View
    {
        $users = User::paginate(10);
        $roles = ['admin', 'user']; // Available roles
        
        return view('admin.users', compact('users', 'roles'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return redirect()->back()->with('success', 'User role updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    /**
     * Generate QR for user
     */
    public function generateUserQR(User $user)
    {
        // Check if method exists to avoid errors
        if (method_exists($user, 'generateProfileQR')) {
            $user->generateProfileQR();
            return redirect()->back()->with('success', 'QR code generated successfully for ' . $user->name);
        }
        
        return redirect()->back()->with('error', 'QR code generation not available');
    }

    /**
     * Show user QR codes
     */
    public function userQRCodes(): View
    {
        $usersWithQR = User::whereNotNull('profile_qr')->paginate(10);
        
        return view('admin.qr-codes', compact('usersWithQR'));
    }
}
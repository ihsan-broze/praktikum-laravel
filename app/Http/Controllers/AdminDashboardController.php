<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseScore;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        cache()->put('dashboard_stats', [
            'users' => User::count(),
            'courses' => Course::count()
        ], 60);

        $cachedStats = cache()->get('dashboard_stats');

        $users = $cachedStats['users'];
        $courses = $cachedStats['courses'];
        $users = User::count();
        $courses = Course::count();

        $categories = Category::withCount('courses')->get()->map(function ($cat) {
            return (object)[
                'title' => $cat->title,
                'category' => $cat->title,
                'total' => $cat->courses_count,
            ];
        });

        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('users', 'courses', 'categories', 'chartData'));
    }

    // Method untuk real-time data
    public function getRealtimeData()
    {
        try {
            $chartData = $this->getChartData();
            
            return response()->json([
                'success' => true,
                'data' => $chartData,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getChartData()
    {
        // 1. Category Distribution
        $categoryData = Category::withCount('courses')
            ->where('courses_count', '>', 0)
            ->get()
            ->map(function ($cat) {
                return [
                    'label' => $cat->title,
                    'count' => $cat->courses_count
                ];
            });

        // 2. Course Status Distribution
        $statusData = Course::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->status),
                    'count' => $item->count
                ];
            });

        // 3. Featured vs Regular
        $featuredData = Course::select('featured', DB::raw('COUNT(*) as count'))
            ->groupBy('featured')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->featured ? 'Featured' : 'Regular',
                    'count' => $item->count
                ];
            });

        // 4. Course Level Distribution
        $levelData = Course::select('level', DB::raw('COUNT(*) as count'))
            ->whereNotNull('level')
            ->groupBy('level')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->level),
                    'count' => $item->count
                ];
            });

        // 5. Course Scoring Data (jika tabel course_scores ada)
        $scoreData = collect();
        if (DB::getSchemaBuilder()->hasTable('course_scores')) {
            $scoreData = CourseScore::select(
                    DB::raw('
                        CASE 
                            WHEN score >= 90 THEN "Excellent (90-100)"
                            WHEN score >= 80 THEN "Good (80-89)"
                            WHEN score >= 70 THEN "Average (70-79)"
                            WHEN score >= 60 THEN "Below Average (60-69)"
                            ELSE "Poor (< 60)"
                        END as score_range
                    '),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('score_range')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->score_range,
                        'count' => $item->count
                    ];
                });
        }

        // 6. Average Score by Category (jika ada data scores)
        $categoryScores = collect();
        if (DB::getSchemaBuilder()->hasTable('course_scores') && CourseScore::count() > 0) {
            $categoryScores = DB::table('course_scores')
                ->join('courses', 'course_scores.course_id', '=', 'courses.id')
                ->join('categories', 'courses.category_id', '=', 'categories.id')
                ->select('categories.title', DB::raw('AVG(course_scores.score) as avg_score'))
                ->groupBy('categories.title')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->title,
                        'score' => round($item->avg_score, 1)
                    ];
                });
        }

        // 7. Completion Rate by Difficulty (jika ada data scores)
        $difficultyCompletion = collect();
        if (DB::getSchemaBuilder()->hasTable('course_scores') && CourseScore::count() > 0) {
            $difficultyCompletion = CourseScore::select(
                    'difficulty_rating',
                    DB::raw('AVG(completion_percentage) as avg_completion')
                )
                ->whereNotNull('difficulty_rating')
                ->groupBy('difficulty_rating')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => ucfirst($item->difficulty_rating),
                        'completion' => round($item->avg_completion, 1)
                    ];
                });
        }

        // 8. Real-time Metrics
        $totalScores = DB::getSchemaBuilder()->hasTable('course_scores') ? CourseScore::count() : 0;
        $avgScore = DB::getSchemaBuilder()->hasTable('course_scores') ? CourseScore::avg('score') : 0;
        $completedCourses = DB::getSchemaBuilder()->hasTable('course_scores') ? CourseScore::where('completion_percentage', 100)->count() : 0;
        
        $realtimeMetrics = [
            'total_scores' => $totalScores,
            'avg_overall_score' => round($avgScore ?? 0, 1),
            'completion_rate' => $totalScores > 0 ? round(($completedCourses / $totalScores) * 100, 1) : 0,
            'last_updated' => now()->format('H:i:s')
        ];

        // 9. Score Trend (Last 30 days)
        $scoreTrend = collect();
        if (DB::getSchemaBuilder()->hasTable('course_scores') && CourseScore::count() > 0) {
            $scoreTrend = CourseScore::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('AVG(score) as avg_score'),
                    DB::raw('COUNT(*) as total_scores')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'avg_score' => round($item->avg_score, 1),
                        'total_scores' => $item->total_scores
                    ];
                });
        }

        return [
            'categories' => $categoryData,
            'status' => $statusData,
            'featured' => $featuredData,
            'levels' => $levelData,
            'scores' => $scoreData,
            'categoryScores' => $categoryScores,
            'difficultyCompletion' => $difficultyCompletion,
            'realtimeMetrics' => $realtimeMetrics,
            'scoreTrend' => $scoreTrend,
            'courseTrend' => collect(),
            'viewedCategories' => collect()
        ];
    }
}
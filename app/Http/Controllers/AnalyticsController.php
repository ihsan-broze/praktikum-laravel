<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function showQueries()
    {
        // Enable query logging
        DB::enableQueryLog();
        
        // Jalankan query yang ingin dianalisis
        $users = User::with('posts.comments')->get();
        
        // Tampilkan semua query yang dijalankan
        $queries = DB::getQueryLog();
        
        // Log query untuk debugging
        foreach ($queries as $query) {
            logger()->info('Query: ' . $query['query']);
            logger()->info('Bindings: ' . json_encode($query['bindings']));
            logger()->info('Time: ' . $query['time'] . 'ms');
        }
        
        return response()->json([
            'total_queries' => count($queries),
            'queries' => $queries
        ]);
    }
}
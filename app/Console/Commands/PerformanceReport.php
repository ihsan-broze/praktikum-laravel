<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceReport extends Command
{
    protected $signature = 'performance:report';
    protected $description = 'Generate performance report';
    
    public function handle()
    {
        $this->info('Generating Performance Report...');
        
        // Database queries analysis
        $slowQueries = DB::select("
            SELECT query_time, sql_text, rows_examined 
            FROM mysql.slow_log 
            WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY query_time DESC 
            LIMIT 10
        ");
        
        $this->table(['Query Time', 'SQL', 'Rows Examined'], $slowQueries);
        
        // Cache hit rate
        $cacheStats = Cache::getRedis()->info('stats');
        $this->info("Cache Hit Rate: " . $cacheStats['keyspace_hits'] / ($cacheStats['keyspace_hits'] + $cacheStats['keyspace_misses']) * 100 . "%");
        
        // Memory usage
        $this->info("Memory Usage: " . memory_get_usage(true) / 1024 / 1024 . " MB");
        
        $this->info('Performance report generated successfully!');
    }
}
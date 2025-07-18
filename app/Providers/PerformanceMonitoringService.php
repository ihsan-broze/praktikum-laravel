<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringService
{
    public function trackSlowQueries()
    {
        DB::listen(function ($query) {
            if ($query->time > 100) { // Query lebih dari 100ms
                Log::warning('Slow Query Detected', [
                    'query' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                ]);
            }
        });
    }
    
    public function trackMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryUsage > (0.8 * $this->convertToBytes($memoryLimit))) {
            Log::warning('High Memory Usage', [
                'current' => $this->formatBytes($memoryUsage),
                'limit' => $memoryLimit,
                'percentage' => round(($memoryUsage / $this->convertToBytes($memoryLimit)) * 100, 2) . '%'
            ]);
        }
    }
    
    private function convertToBytes($value)
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }
    
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}
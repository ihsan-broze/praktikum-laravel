<?php

namespace App\Watchers;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\Watchers\Watcher;

class PerformanceWatcher extends Watcher
{
    public function register($app)
    {
        $app['events']->listen('*', [$this, 'recordEvent']);
    }
    
    public function recordEvent($event, $payload)
    {
        if (!$this->shouldRecord($event)) {
            return;
        }
        
        $startTime = microtime(true);
        
        // Monitor performance critical events
        if (str_contains($event, 'query') || str_contains($event, 'cache')) {
            Telescope::recordPerformance(
                IncomingEntry::make([
                    'event' => $event,
                    'payload' => $payload,
                    'execution_time' => microtime(true) - $startTime,
                    'memory_usage' => memory_get_usage(true),
                ])
            );
        }
    }
    
    private function shouldRecord($event)
    {
        return !in_array($event, [
            'telescope.*',
            'debugbar.*',
        ]);
    }
}
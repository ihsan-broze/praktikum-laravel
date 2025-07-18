<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class KpiCard extends Component
{
    public $title;
    public $value;
    public $trend;
    public $trendValue;
    public $valueId;
    public $gradient;
    public $icon;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = 'KPI Title',
        string $value = '0',
        string $trend = '',
        string $trendValue = '',
        string $valueId = '',
        string $gradient = 'from-blue-500 to-blue-600'
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->trend = $trend;
        $this->trendValue = $trendValue;
        $this->valueId = $valueId ?: strtolower(str_replace(' ', '-', $title));
        $this->gradient = $gradient;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.kpi-card');
    }
}
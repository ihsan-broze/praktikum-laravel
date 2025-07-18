<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChartCard extends Component
{
    public $title;
    public $chartId;
    public $height;
    public $colspan;
    public $actions;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = 'Chart Title',
        string $chartId = 'defaultChart',
        string $height = 'h-64',
        string $colspan = '',
        $actions = null
    ) {
        $this->title = $title;
        $this->chartId = $chartId;
        $this->height = $height;
        $this->colspan = $colspan;
        $this->actions = $actions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chart-card');
    }
}
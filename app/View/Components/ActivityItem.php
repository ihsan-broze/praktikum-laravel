<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ActivityItem extends Component
{
    public $type;
    public $user;
    public $action;
    public $time;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $type = 'info',
        string $user = '',
        string $action = '',
        string $time = ''
    ) {
        $this->type = $type;
        $this->user = $user;
        $this->action = $action;
        $this->time = $time ?: now()->format('H:i:s');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.activity-item');
    }
}
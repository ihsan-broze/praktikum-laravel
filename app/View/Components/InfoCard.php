<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InfoCard extends Component
{
    public $title;
    public $icon;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = 'Info Card',
        string $icon = ''
    ) {
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.info-card');
    }
}
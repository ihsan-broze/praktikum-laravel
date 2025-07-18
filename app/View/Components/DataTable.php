<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public $title;
    public $tableId;
    public $bodyId;
    public $searchable;
    public $headers;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title = 'Data Table',
        string $tableId = 'dataTable',
        string $bodyId = 'dataTableBody',
        bool $searchable = false,
        array $headers = []
    ) {
        $this->title = $title;
        $this->tableId = $tableId;
        $this->bodyId = $bodyId;
        $this->searchable = $searchable;
        $this->headers = $headers;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.data-table');
    }
}
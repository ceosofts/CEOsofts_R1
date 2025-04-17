<?php

namespace App\View\Components;

use App\Models\BranchOffice;
use Illuminate\View\Component;
use Illuminate\View\View;

class BranchOfficeCard extends Component
{
    /**
     * The branch office object.
     *
     * @var BranchOffice
     */
    public $branchOffice;

    /**
     * Whether to show detailed information.
     *
     * @var bool
     */
    public $detailed;

    /**
     * Create a new component instance.
     *
     * @param  BranchOffice  $branchOffice
     * @param  bool  $detailed
     * @return void
     */
    public function __construct(BranchOffice $branchOffice, bool $detailed = false)
    {
        $this->branchOffice = $branchOffice;
        $this->detailed = $detailed;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('components.branch-office-card');
    }
}

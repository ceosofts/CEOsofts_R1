<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class BreadcrumbNav extends Component
{
    /**
     * The breadcrumbs data.
     *
     * @var array
     */
    public $breadcrumbs;

    /**
     * Whether to show the home icon.
     *
     * @var bool
     */
    public $showHomeIcon;

    /**
     * Create a new component instance.
     *
     * @param array $breadcrumbs
     * @param bool $showHomeIcon
     * @return void
     */
    public function __construct($breadcrumbs = [], $showHomeIcon = true)
    {
        // Convert from string to array if necessary
        if (is_string($breadcrumbs)) {
            $breadcrumbs = json_decode($breadcrumbs, true) ?? [];
        }
        
        // Ensure home link is always first
        if ($showHomeIcon && !isset($breadcrumbs['หน้าหลัก']) && !in_array('หน้าหลัก', array_keys($breadcrumbs))) {
            $this->breadcrumbs = ['หน้าหลัก' => route('dashboard')] + (is_array($breadcrumbs) ? $breadcrumbs : []);
        } else {
            $this->breadcrumbs = is_array($breadcrumbs) ? $breadcrumbs : [];
        }

        $this->showHomeIcon = $showHomeIcon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('components.breadcrumb-nav');
    }
}

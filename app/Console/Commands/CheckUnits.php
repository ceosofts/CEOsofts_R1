<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Unit;

class CheckUnits extends Command
{
    protected $signature = 'units:check';
    protected $description = 'Check units data in database';

    public function handle()
    {
        $units = Unit::all();
        $this->info("Found {$units->count()} units");
        
        foreach ($units as $unit) {
            $this->info("Unit ID: {$unit->id}, Name: {$unit->name}, Code: {$unit->code}, Description: {$unit->description}");
        }
        
        return 0;
    }
}

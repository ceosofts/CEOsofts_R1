<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDatabaseTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if required tables exist in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $requiredTables = [
            'employees',
            'companies',
            'departments',
            'positions',
            'branch_offices',
            'users',
        ];

        $this->info('Checking database tables...');

        $connection = config('database.default');
        $this->info("Using database connection: {$connection}");

        if ($connection === 'sqlite') {
            $tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;"))
                ->pluck('name')->toArray();
        } else {
            $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        }

        $this->info("\nAvailable tables:");
        foreach ($tables as $table) {
            $this->line(" - {$table}");
        }

        $this->info("\nRequired tables check:");
        $allExist = true;

        foreach ($requiredTables as $table) {
            $exists = in_array($table, $tables);
            $this->line(" - {$table}: " . ($exists ? '✓' : '✗'));

            if (!$exists) {
                $allExist = false;
            }
        }

        if ($allExist) {
            $this->info("\nAll required tables exist.");
        } else {
            $this->error("\nSome required tables are missing!");
            return 1;
        }

        return 0;
    }
}

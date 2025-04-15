<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListAllTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:tables {--count : Count records in each table} {--structure : Show table structure}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tables in the database with optional record counts and structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = config('database.default');
        $this->info("Using database connection: {$connection}");
        
        // Get all tables
        if ($connection === 'sqlite') {
            $tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;"))
                ->pluck('name')
                ->filter(fn($table) => !in_array($table, ['sqlite_sequence']));
        } else {
            $tables = Schema::getAllTables();
        }
        
        if ($tables->isEmpty()) {
            $this->error('No tables found in the database.');
            return 1;
        }
        
        $headers = ['Table Name'];
        $rows = [];
        
        if ($this->option('count')) {
            $headers[] = 'Records';
        }
        
        foreach ($tables as $table) {
            $tableName = is_object($table) ? $table->name : $table;
            
            if ($tableName === 'migrations' || $tableName === 'sqlite_sequence') {
                continue;
            }
            
            $row = [$tableName];
            
            if ($this->option('count')) {
                try {
                    $count = DB::table($tableName)->count();
                    $row[] = $count;
                } catch (\Exception $e) {
                    $row[] = 'Error: ' . $e->getMessage();
                }
            }
            
            $rows[] = $row;
        }
        
        $this->table($headers, $rows);
        
        // Show table structure if requested
        if ($this->option('structure')) {
            foreach ($tables as $table) {
                $tableName = is_object($table) ? $table->name : $table;
                
                if ($tableName === 'migrations' || $tableName === 'sqlite_sequence') {
                    continue;
                }
                
                $this->info("\nStructure for table: {$tableName}");
                
                $columns = Schema::getColumnListing($tableName);
                $columnInfo = [];
                
                foreach ($columns as $column) {
                    $type = DB::connection()->getDoctrineColumn($tableName, $column)->getType()->getName();
                    $columnInfo[] = [
                        'Column' => $column,
                        'Type' => $type,
                    ];
                }
                
                $this->table(['Column', 'Type'], $columnInfo);
            }
        }
        
        return 0;
    }
}

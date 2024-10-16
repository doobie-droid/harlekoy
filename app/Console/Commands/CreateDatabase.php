<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    protected $signature = 'db:create {name}';
    protected $description = 'Create the specified database if it does not exist';

    public function handle()
    {
        $databaseName = $this->argument('name');

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            $this->info("Database '{$databaseName}' created or already exists.");
        } catch (\Exception $e) {
            $this->error("Error creating database: " . $e->getMessage());
        }
    }
}

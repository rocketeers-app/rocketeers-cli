<?php

namespace App\Commands;

use Illuminate\Console\Command;

class DatabaseConnections extends Command
{
    protected $signature = 'db:connections {app}';
    protected $description = 'Fetch database connections for database app';

    public function handle()
    {
    }
}

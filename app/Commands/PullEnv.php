<?php

namespace App\Commands;

use App\Actions\GetEnv;
use Illuminate\Console\Command;

class PullEnv extends Command
{
    protected $signature = 'tail:log {site} {--server=}';
    protected $description = 'Tail logs from sites';

    public function handle()
    {
        $site = $this->argument('site');
        $server = $this->option('server') ?? $site;

        echo (new GetEnv)($server, $site);
    }
}

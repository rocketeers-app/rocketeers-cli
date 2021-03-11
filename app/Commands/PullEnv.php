<?php

namespace App\Commands;

use App\Actions\GetEnv;
use Illuminate\Console\Command;

class PullEnv extends Command
{
    protected $signature = 'env:pull {site} {--server=}';
    protected $description = 'Pull env file';

    public function handle()
    {
        $site = $this->argument('site');
        $server = $this->option('server') ?? $site;

        echo (new GetEnv)($server, $site);
    }
}

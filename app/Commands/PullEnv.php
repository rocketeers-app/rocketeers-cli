<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Spatie\Ssh\Ssh;

class PullEnv extends Command
{
    protected $signature = 'env:pull {site} {--server=}';
    protected $description = 'Pull env file';

    public function handle()
    {
        $site = $this->argument('site');
        $server = $this->option('server') ?? $site;

        $process = Ssh::create('rocketeer', $server)
            ->execute("cat /var/www/{$site}/.env");

        echo $process->getOutput();
    }
}

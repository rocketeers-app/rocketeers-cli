<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Spatie\Ssh\Ssh;

class PullEnv extends Command
{
    protected $signature = 'env:pull {site}';
    protected $description = 'Pull env file';

    public function handle()
    {
        $site = $this->argument('site');

        $process = Ssh::create('rocketeer', $site)
            ->execute("cat /var/www/{$site}/.env");

        echo $process->getOutput();
    }
}

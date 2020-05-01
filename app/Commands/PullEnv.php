<?php

namespace App\Commands;

use Spatie\Ssh\Ssh;
use Illuminate\Console\Command;

class PullEnv extends Command
{
    protected $signature = 'env:pull {site}';
    protected $description = 'Pull env file';

    public function handle()
    {
        $site = $this->argument('site');

        $process = Ssh::create('rocketeer', $site)
            ->execute("cat /var/www/{$site}/.env");

        $process->run();

        echo $process->getOutput();
    }
}

<?php

namespace App\Commands;

use Spatie\Ssh\Ssh;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TailLog extends Command
{
    protected $signature = 'tail:log {site}';
    protected $description = 'Pull env file';

    public function handle()
    {
        $site = $this->argument('site');

        $process = Ssh::create('rocketeer', $site)
        ->configureProcess(fn (Process $process) => $process->setTty(true))
        ->onOutput(function ($type, $line) {
            $this->handleClearOption();

            $this->output->write($line);
        })
        ->execute([
            "tail -f /var/www/{$site}/current/storage/logs/laravel.log",
        ]);
    }
}

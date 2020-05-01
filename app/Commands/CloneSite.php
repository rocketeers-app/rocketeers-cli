<?php

namespace App\Commands;

use Spatie\Ssh\Ssh;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CloneSite extends Command
{
    protected $signature = 'clone {site}';
    protected $description = 'Clone site';

    public function handle()
    {
        $site = $this->argument('site');
        
        $process = Ssh::create('rocketeer', $site)
            ->execute("cd /var/www/{$site}/current && git config --get remote.origin.url");

        $repositoryUrl = trim($process->getOutput());

        $process = new Process(['git', 'clone', $repositoryUrl, $site]);
        $process->setTty(Process::isTtySupported());
        $process->run();

        $process = Process::fromShellCommandline("cd {$site} && composer install && cp .env.example .env && php artisan migrate");
        $process->setTty(Process::isTtySupported());
        $process->run();

        echo $process->getOutput();
    }
}

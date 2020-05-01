<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ImportDatabase extends Command
{
    protected $signature = 'db:import {site}';
    protected $description = 'Import database';

    public function handle()
    {
        $site = $this->argument('site');
        $password = $this->ask('Please provide database password');

        $process = Process::fromShellCommandline("ssh rocketeer@{$site} 'mysqldump -u {$site} --password=\"{$password}\" {$site} | gzip' | gunzip | mysql -u root --password=\"\" {$site}");

        $process->run(null, ['site' => $site, 'password' => $password]);
    }
}

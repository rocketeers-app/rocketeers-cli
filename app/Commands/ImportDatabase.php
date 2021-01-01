<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Spatie\Ssh\Ssh;
use Symfony\Component\Process\Process;

class ImportDatabase extends Command
{
    protected $signature = 'db:import {site}';
    protected $description = 'Import database';

    public function handle()
    {
        $site = $this->argument('site');

        $process = Ssh::create('rocketeer', $site)
            ->execute([
                'grep DB_PASSWORD /var/www/'.$site."/.env | grep -v -e '^\s*#' | cut -d '=' -f 2-",
            ]);

        $password = trim($process->getOutput());

        $process = Process::fromShellCommandline("mysql -u root --password='' -e 'CREATE DATABASE IF NOT EXISTS `'".$site."'` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        $process->run();

        $process = Process::fromShellCommandline('ssh rocketeer@'.$site.' "sudo mysqldump --user=\''.$site.'\' --password=\''.$password.'\' --no-tablespaces \''.$site.'\' | sudo gzip" | gunzip | mysql -u root --password=\'\' \''.$site.'\'');
        $process->run();
    }
}

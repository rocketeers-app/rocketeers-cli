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
        $password = $this->secret('Please provide database password');

        // $process = Process::fromShellCommandline("ssh rocketeer@{$site} 'cd /var/www/".$site." && grep DB_PASSWORD .env | grep -v -e '^\s*#' | cut -d '=' -f 2-)'");

        // dd('ssh rocketeer@{$site} "grep DB_PASSWORD /var/www/'.$site.'/.env | grep -v -e \'^\s*#\' | cut -d \'=\' -f 2-)"');
        // echo $process->run();
        // exit;

        $process = Process::fromShellCommandline('mysql -u root --password="" -e "CREATE DATABASE IF NOT EXISTS '.$site.' CHARACTER SET utf8 COLLATE utf8_general_ci"');
        echo $process->run();

        $process = Process::fromShellCommandline("ssh rocketeer@{$site} 'sudo mysqldump -u {$site} --password=\"{$password}\" {$site} | sudo gzip' | gunzip | mysql -u root --password=\"\" {$site}");
        dd("ssh rocketeer@{$site} 'sudo mysqldump -u {$site} --password=\"{$password}\" {$site} | sudo gzip' | gunzip | mysql -u root --password=\"\" {$site}");
        echo $process->run(null, ['site' => $site, 'password' => $password]);
    }
}

<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Ssh\Ssh;
use Symfony\Component\Process\Process;

class ImportRemoteDatabase
{
    use AsAction;

    public function handle($site, $server = null)
    {
        $process = Ssh::create('rocketeer', $server)
            ->execute([
                'grep DB_USERNAME /var/www/'.$site."/current/.env | grep -v -e '^\s*#' | cut -d '=' -f 2-",
            ]);

        $username = trim($process->getOutput());

        $process = Ssh::create('rocketeer', $server)
            ->execute([
                'grep DB_PASSWORD /var/www/'.$site."/current/.env | grep -v -e '^\s*#' | cut -d '=' -f 2-",
            ]);

        $password = trim($process->getOutput());

        $process = Process::fromShellCommandline("mysql -u root --password='' -e 'CREATE DATABASE IF NOT EXISTS `'".$site."'` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        $process->run();

        $process = Process::fromShellCommandline('ssh rocketeer@'.$server.' "sudo mysqldump --user=\''.$username.'\' --password=\''.$password.'\' --no-tablespaces \''.$site.'\' | sudo gzip" | gunzip | mysql -u root --password=\'\' \''.$site.'\'');
        $process->run();
    }
}

<?php

namespace App\Commands;

use App\Actions\GetCurrentBranch;
use App\Actions\GetEnv;
use App\Actions\GetRepositoryName;
use App\Actions\GetRepositoryUrl;
use App\Actions\ImportRemoteDatabase;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Install extends Command
{
    protected $signature = 'install {site} {--server=}';
    protected $description = 'Install site';

    public function handle()
    {
        $site = $this->argument('site');
        $server = $this->option('server') ?? $site;

        $url = (new GetRepositoryUrl)($site, $server);
        $name = (new GetRepositoryName)($site, $server);
        $branch = (new GetCurrentBranch)($site, $server);

        $process = new Process(['git', 'clone', $url, '/var/www/'.$name]);
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(300);
        $process->run();

        $process = new Process(['git', 'checkout', $branch]);
        $process->setTty(Process::isTtySupported());
        $process->run();

        $env = (new GetEnv)($site, $server);

        $env = preg_replace('/^APP_ENV=(.*)/m', 'APP_ENV=local', $env);
        $env = preg_replace('/^APP_DEBUG=(.*)/m', 'APP_ENV=true', $env);
        $env = preg_replace('/^CACHE_DRIVER=(.*)/m', 'CACHE_DRIVER=array', $env);
        $env = preg_replace('/^DB_HOST=(.*)/m', 'DB_HOST=127.0.0.1', $env);
        $env = preg_replace('/^DB_DATABASE=(.*)/m', 'DB_DATABASE='.$name, $env);
        $env = preg_replace('/^DB_USERNAME=(.+)/m', 'DB_USERNAME=root', $env);
        $env = preg_replace('/^DB_PASSWORD=(.*)/m', 'DB_PASSWORD=', $env);

        file_put_contents("/var/www/{$name}/.env", $env);

        (new ImportRemoteDatabase)($site, $server);

        $process = Process::fromShellCommandline("cd /var/www/{$name} && composer install");
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(300);
        $process->run();

        $process = Process::fromShellCommandline("cd /var/www/{$name} && php artisan migrate --force");
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(300);
        $process->run();

        $process = Process::fromShellCommandline("cd /var/www/{$name} && npm install && npm run dev");
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(300);
        $process->run();

        $process = Process::fromShellCommandline("cd /var/www/{$name} && valet secure {$name}");
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(300);
        $process->run();

        $this->notify('Site is installed and ready to rocket!');
    }
}

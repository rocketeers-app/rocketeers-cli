<?php

namespace App\Commands;

use Illuminate\Console\Command;

class SshConfig extends Command
{
    protected $signature = 'ssh:config';
    protected $description = 'Get SSH config for all hosts';

    public function handle()
    {
    }
}

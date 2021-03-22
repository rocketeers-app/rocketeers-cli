<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Process\Process;

class CheckoutBranchLocally
{
    use AsAction;

    public function handle($name, $branch)
    {
        $process = Process::fromShellCommandline("cd /var/www/{$name} && git checkout {$branch}");
        $process->setTty(Process::isTtySupported());
        $process->run();
    }
}

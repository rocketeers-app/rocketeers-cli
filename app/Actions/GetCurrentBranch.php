<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Ssh\Ssh;

class GetCurrentBranch
{
    use AsAction;

    public function handle($site, $server = null)
    {
        $process = Ssh::create('rocketeer', $server ?? $site)
            ->execute("cd /var/www/{$site}/current && git rev-parse --abbrev-ref HEAD");

        return trim($process->getOutput());
    }
}

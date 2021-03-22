<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class NotifyLocally
{
    use AsAction;

    public function handle($message)
    {
        // $this->notify($message, 'icon.png');
    }
}

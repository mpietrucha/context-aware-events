<?php

namespace Mpietrucha\Events;

use Mpietrucha\Events\Event\Closure;

class Test extends Closure
{
    public function handle(): void
    {
        $this->cli()->components()->info('xd');
    }
}

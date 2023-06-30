<?php

namespace Mpietrucha\Events\Event;

use Mpietrucha\Events\Callback;

abstract class Process extends Closure
{
    public function before(Callback $callback): void
    {
        $callback->assertProcessContext();
    }
}

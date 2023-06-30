<?php

namespace Mpietrucha\Events\Contracts;

use Mpietrucha\Events\Callback;

interface EventInterface
{
    public function handle(Callback $callback): void;
}

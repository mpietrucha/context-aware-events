<?php

namespace Mpietrucha\Events\Contracts;

use Mpietrucha\Events\Callback;

interface EventInterface
{
    public function handle(): void;

    public function run(Callback $callback): void;
}

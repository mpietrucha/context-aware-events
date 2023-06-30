<?php

namespace Mpietrucha\Events\Contracts;

use Mpietrucha\Events\Callback;

interface EventInterface
{
    public function run(Callback $callback): void;

    public function handle(): void;
}

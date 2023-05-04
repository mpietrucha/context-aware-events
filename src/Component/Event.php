<?php

namespace Mpietrucha\Events\Component;

use Mpietrucha\Events\Bootstrap;
use Mpietrucha\Events\Factory\Router;

class Event extends Router
{
    public function getGlobalEvents(): array
    {
        return [Bootstrap::beforeEvent(...), Bootstrap::afterEvent(...)];
    }

    public function getStorageAccessor(): string
    {
        return 'add';
    }
}

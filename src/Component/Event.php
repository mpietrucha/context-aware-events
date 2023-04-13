<?php

namespace Mpietrucha\Events\Component;

use Mpietrucha\Events\Factory\Router;

class Event extends Router
{
    public function getStorageAccessor(): string
    {
        return 'add';
    }
}

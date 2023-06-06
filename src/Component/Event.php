<?php

namespace Mpietrucha\Events\Component;

use Mpietrucha\Events\Bootstrap;

class Event extends Component
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

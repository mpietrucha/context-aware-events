<?php

namespace Mpietrucha\Events\Component;

use Closure;
use Mpietrucha\Events\Factory\Router;
use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Result;

class Dispatcher extends Router
{
    public function getStorageAccessor(): string
    {
        return 'get';
    }

    public function handle(StorageInterface $storage, Result $result): void
    {
        $result->get()->each(fn (Closure $callback) => $callback());

        $storage->delete($result->event());
    }
}

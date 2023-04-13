<?php

namespace Mpietrucha\Events\Component;

use Closure;
use Mpietrucha\Events\Result;
use Mpietrucha\Events\Factory\Router;
use Mpietrucha\Events\Contracts\StorageInterface;

class Dispatcher extends Router
{
    public function getStorageAccessor(): string
    {
        return 'get';
    }

    public function handle(Result $result, StorageInterface $storage, ?string $context, string $caller): void
    {
        dd($context, $caller);
        $result->get()->each(fn (Closure $callback) => $callback());

        $storage->delete($result->event());
    }
}

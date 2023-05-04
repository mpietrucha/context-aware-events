<?php

namespace Mpietrucha\Events\Component;

use Mpietrucha\Events\Result;
use Mpietrucha\Events\Process;
use Mpietrucha\Events\Bootstrap;
use Illuminate\Support\Collection;
use Mpietrucha\Events\Factory\Router;
use Mpietrucha\Events\Contracts\StorageInterface;

class Dispatcher extends Router
{
    public function getGlobalEvents(): array
    {
        return [Bootstrap::beforeDispatch(...), Bootstrap::afterDispatch(...)];
    }

    public function getStorageAccessor(): string
    {
        return 'get';
    }

    public function handle(StorageInterface $storage, Result $result): void
    {
        $result->get()->each(fn (Collection $results) => $result->events($storage, function () use ($results) {
            return Process::create(...$results);
        }));

        $storage->delete($result->event());
    }
}

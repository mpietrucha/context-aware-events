<?php

namespace Mpietrucha\Events\Factory;

use Mpietrucha\Events\Context;
use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Result;
use Mpietrucha\Events\Contracts\ComponentInterface;

abstract class Router implements ComponentInterface
{
    public static function __callStatic(string $method, array $arguments)
    {
        [$accessor, $handler] = with(new static, fn (self $instance) => [
            $instance->getStorageAccessor(),
            $instance->handle(...)
        ]);

        return Context::create($accessor, $method, $handler, ...$arguments);
    }

    public function handle(StorageInterface $storage, Result $result): void
    {
    }
}

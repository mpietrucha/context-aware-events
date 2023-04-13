<?php

namespace Mpietrucha\Events\Factory;

use Mpietrucha\Events\Result;
use Mpietrucha\Events\Context;
use Mpietrucha\Events\Bootstrap;
use Mpietrucha\Events\Contracts\ComponentInterface;
use Mpietrucha\Events\Contracts\StorageInterface;

abstract class Router implements ComponentInterface
{
    public static function __callStatic(string $method, array $arguments): Context
    {
        [$accessor, $handler] = with(new static, fn (self $component) => [
            $component->getStorageAccessor(),
            $component->handle(...)
        ]);

        return Context::create($accessor, $method, $handler);
    }

    public function handle(Result $result, StorageInterface $storage, ?string $context, string $caller): void
    {
    }
}

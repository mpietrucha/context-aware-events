<?php

namespace Mpietrucha\Events\Contracts;

use Closure;
use Mpietrucha\Events\Result;

interface StorageInterface
{
    public function add(string $event, ?string $context, Closure $callback): Result;

    public function get(string $event, ?string $context): Result;

    public function delete(string $event): void;
}

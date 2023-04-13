<?php

namespace Mpietrucha\Events\Contracts;

use Closure;
use Mpietrucha\Events\Result;

interface StorageInterface
{
    public function add(string $event, array $contexts, string $caller, Closure $callback): Result;

    public function get(string $event, array $contexts, string $caller): Result;

    public function delete(string $event): void;
}

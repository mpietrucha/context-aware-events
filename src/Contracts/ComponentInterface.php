<?php

namespace Mpietrucha\Events\Contracts;

use Mpietrucha\Events\Result;
use Mpietrucha\Events\Contracts\StorageInterface;

interface ComponentInterface
{
    public function getStorageAccessor(): string;

    public function handle(Result $result, StorageInterface $storage, ?string $context, string $caller): void;
}

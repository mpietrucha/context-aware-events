<?php

namespace Mpietrucha\Events\Contracts;

use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Result;

interface ComponentInterface
{
    public function getStorageAccessor(): string;

    public function handle(StorageInterface $storage, Result $result): void;
}

<?php

namespace Mpietrucha\Events\Storage;

use Closure;
use Mpietrucha\Storage\File;
use Mpietrucha\Events\Result;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasVendor;
use Mpietrucha\Events\Contracts\StorageInterface;

class TemporaryFilesystemStorage implements StorageInterface
{
    use HasVendor;

    protected File $storage;

    public function __construct()
    {
        $this->storage = File::create()->prefix($this->vendor())->temporary()->shared();
    }

    public function add(string $event, array $contexts, string $caller, Closure $callback): Result
    {
        $this->storage->append($event, [$callback, $contexts]);

        return $this->get($event, $contexts, $caller);
    }

    public function get(string $event, array $contexts, string $caller): Result
    {
        return Result::create(
            $this->storage->get($event) ?? collect(),
            $event, $contexts, $caller
        );
    }

    public function delete(string $event): void
    {
        $this->storage->forget($event);
    }
}

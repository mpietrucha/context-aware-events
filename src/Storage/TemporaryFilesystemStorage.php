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

    public function add(string $event, ?string $context, Closure $callback): Result
    {
        $this->storage->append($event, [$callback, $context]);

        return $this->get($event);
    }

    public function get(string $event, ?string $context): Result
    {
        dd($event, $contex);
        $result = $this->storage->get($event)->filter(function (Collection $entry) use ($context) {

        });

        return Result::create($event, $result);
    }

    public function delete(string $event): void
    {
        $this->storage->forget($event);
    }
}

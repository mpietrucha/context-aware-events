<?php

namespace Mpietrucha\Events;

use Closure;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Concerns\HasVendor;
use Mpietrucha\Events\Result;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Events\Contracts\StorageInterface;

class Context
{
    use HasVendor;
    use HasFactory;

    protected ?string $context = null;

    protected ?Result $result = null;

    public function __construct(protected string $accessor, protected string $event, protected Closure $handler)
    {
    }

    public function __destruct()
    {
        $this->result();
    }

    public function result(): Result
    {
        if (! $this->result) {
            $storage = Bootstrap::create();

            $this->result = $storage->{$this->accessor}($this->event, $this->context);

            ($this->handler)($this->result, $storage, $this->context, $this->vendor()->path());
        }

        return $this->result;
    }

    public function context(string $context): void
    {
        $this->context = $content;
    }

    public function current(): void
    {
        $this->context($this->vendor()->path());
    }
}

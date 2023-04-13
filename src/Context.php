<?php

namespace Mpietrucha\Events;

use Closure;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Concerns\HasFactory;

class Context
{
    use HasFactory;

    protected string $caller;

    protected array $contexts = [];

    protected ?Result $result = null;

    public function __construct(protected string $accessor, protected string $event, protected Closure $handler, protected ?Closure $callback = null)
    {
        $this->caller = Vendor::create()->path();
    }

    public function __destruct()
    {
        $this->result();
    }

    public function result(): Result
    {
        if (! $this->result) {
            $storage = Bootstrap::create();

            $this->result = $storage->{$this->accessor}($this->event, $this->contexts, $this->caller, $this->callback);

            ($this->handler)($storage, $this->result);
        }

        return $this->result;
    }

    public function context(string|array $contexts): self
    {
        $this->contexts = Arr::wrap($contexts);

        return $this;
    }

    public function app(): self
    {
        return $this->context([
            Vendor::create()->root()->path()
        ]);
    }
}

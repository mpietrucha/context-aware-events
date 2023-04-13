<?php

namespace Mpietrucha\Events;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;

class Result
{
    use HasFactory;

    public function __construct(protected string $event, protected Collection $result)
    {
    }

    public function event(): string
    {
        return $this->event;
    }

    protected function get(): Collection
    {
        return $this->result;
    }
}

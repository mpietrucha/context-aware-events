<?php

namespace Mpietrucha\Events\Contracts;

interface AutoloaderInterface
{
    public function name(): string;

    public function autoload(): void;
}

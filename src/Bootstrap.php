<?php

namespace Mpietrucha\Events;

use Closure;
use Psr\Log\LoggerInterface;
use Mpietrucha\Support\Bootstrapper;
use Mpietrucha\Events\Concerns\GlobalEvents;
use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Storage\TemporaryFilesystemStorage;

class Bootstrap
{
    use GlobalEvents;

    protected static ?Closure $output = null;

    protected static ?LoggerInterface $logger = null;

    protected static ?StorageInterface $storage = null;

    protected static ?Bootstrapper $bootstrapper = null;

    public static function create(): StorageInterface
    {
        return self::$storage ??= new TemporaryFilesystemStorage;
    }

    public static function logger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function storage(StorageInterface $storage): void
    {
        self::$storage = $storage;
    }

    public static function bootstrapper(Bootstrapper $bootstrapper): void
    {
        self::$bootstrapper = $bootstrapper;
    }

    public static function output(null|Closure $configurator = null): void
    {
        self::$output = $configurator;
    }
}

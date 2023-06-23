<?php

namespace Mpietrucha\Events\Concerns;

use Mpietrucha\Events\Result;
use Mpietrucha\Events\Callback;
use Mpietrucha\Events\Process;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Events\Contracts\StorageInterface;

trait GlobalEvents
{
    public static function before(StorageInterface $storage, Result $result): void
    {
        self::log('starting');
    }

    public static function beforeEvent(StorageInterface $storage, Result $result): void
    {
        Process::setConfigurator(self::$output);

        self::log('starting event', [
            'event' => $result->event()
        ]);
    }

    public static function beforeDispatch(StorageInterface $storage, Result $result): void
    {
        Callback::setBootstrapper(self::$bootstrapper);

        self::log('starting dispatch', [
            'event' => $result->event()
        ]);
    }

    public static function after(StorageInterface $storage, Result $result): void
    {
        self::log('ending');
    }

    public static function afterEvent(StorageInterface $storage, Result $result): void
    {
        self::log('ending event', [
            'event' => $result->event()
        ]);
    }

    public static function afterDispatch(StorageInterface $storage, Result $result): void
    {
        Callback::setBootstrapper(null);

        self::log('ending dispatch', [
            'event' => $result->event()
        ]);
    }

    protected static function log(string $message, array $context = [], string $vendor = new Vendor): void
    {
        self::$logger?->info("$vendor $message", $context);
    }
}

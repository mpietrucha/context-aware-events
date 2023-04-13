<?php

namespace Mpietrucha\Events;

use Closure;
use Mpietrucha\Storage\File;
use Mpietrucha\Support\Types;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Events\Autoloader\FinderAutoloader;
use Mpietrucha\Events\Autoloader\ClosureAutoloader;
use Mpietrucha\Events\Autoloader\PathAutoloader;
use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Contracts\AutoloaderInterface;
use Mpietrucha\Events\Storage\TemporaryFilesystemStorage;

class Bootstrap
{
    protected static ?int $contextCompareMode = null;

    protected static array $autoloaderFindableDirectories = [];

    protected static ?File $internal = null;

    protected static ?StorageInterface $instance = null;

    protected static ?AutoloaderInterface $autoloader = null;

    public static function create(): StorageInterface
    {
        Result::setContextCompareMode(self::$contextCompareMode);

        return self::$instance ??= self::internal()->get('storage') ?? new TemporaryFilesystemStorage;
    }

    public static function storage(StorageInterface $storage): void
    {
        self::internal()->put('storage', $storage);
    }

    public static function autoloaderFindableDirectories(string|array $directories): void
    {
        self::$autoloaderFindableDirectories = Arr::wrap($directories);
    }

    public static function contextCompareMode(int $contextCompareMode): void
    {
        self::$contextCompareMode = $contextCompareMode;
    }

    public static function autoloader(null|string|Closure|AutoloaderInterface $autoloader): AutoloaderInterface
    {
        if (self::$autoloader) {
            return self::$autoloader;
        }

        if ($autoloader = self::internal()->get('autoloader')) {
            return self::$autoloader = $autoloader;
        }

        if ($autoloader instanceof Closure) {
            $autoloader = new ClosureAutoloader($autoloader);
        }

        if (Types::string($autoloader)) {
            $autoloader = new PathAutoloader($autoloader);
        }

        if (! $autoloader) {
            $autoloader = new FinderAutoloader(self::$autoloaderFindableDirectories);
        }

        self::internal()->put('autoloader', $autoloader);

        return self::$autoloader = $autoloader;
    }

    protected static function internal(): File
    {
        return self::$internal ??= File::create()->prefix(Vendor::create())->temporary()->shared();
    }
}

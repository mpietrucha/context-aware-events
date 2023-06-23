<?php

namespace Mpietrucha\Events;

use SplFileInfo;
use Mpietrucha\Support\Hash;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Composer;
use Mpietrucha\Finder\ProgressiveFinder;
use Mpietrucha\Support\Concerns\HasFactory;

class Version
{
    use HasFactory;

    protected ?string $mismatched = null;

    public function __construct(protected ?string $path, protected Vendor $source)
    {
    }

    public function mismatched(): ?string
    {
        return $this->mismatched;
    }

    public function failed(bool $composer = false): bool
    {
        if (! $package = $this->package()) {
            return $this->withError();
        }

        if (Hash::md5(File::toSplFileInfo($this->source->path())) === Hash::md5(File::toSplFileInfo($package->path()))) {
            return false;
        }

        if ($composer) {
            return $this->withError($package->path());
        }

        Composer::require($package)->tty();

        return $this->failed(true);
    }

    protected function package(): ?Vendor
    {
        if (! $this->path) {
            return null;
        }

        if (! $path = ProgressiveFinder::create($this->path)->directories()->path($this->source)->first()) {
            return null;
        }

        return Vendor::create($path);
    }

    protected function withError(?string $path = null): bool
    {
        $this->mismatched = $path ?? 'unknown path';

        return true;
    }

    // protected ?string $mismatched = null;
    //
    // public function __construct(protected Vendor $path, protected Vendor $source)
    // {
    // }
    //
    // public function failed(bool $composer = false): bool
    // {
    //     $package = $this->package();
    //
    //     if (! $package = $this->package()) {
    //         return $this->withError(File::dirname($this->path));
    //     }
    //
    //     if (Hash::md5(File::toSplFileInfo($this->source->path())) === Hash::md5(File::toSplFileInfo($package))) {
    //         return false;
    //     }
    //
    //     if ($composer) {
    //         return $this->withError($package);
    //     }
    //
    //     dd($this->path);
    //     // Composer::require($this->vendor())->in($p);
    //
    //     return $this->failed(true);
    // }
    //
    // public function mismatched(): ?string
    // {
    //     if ($this->mismatched === null) {
    //         return null;
    //     }
    //
    //     return str($this->mismatched)->whenEmpty(fn () => 'unknown path');
    // }
    //
    // protected function package(): ?SplFileInfo
    // {
    //     return ProgressiveFinder::create($this->path)->directories()->path($this->source)->first();
    // }
    //
    // protected function withError(string $mismatched): bool
    // {
    //     $this->mismatched = $mismatched;
    //
    //     return true;
    // }
}

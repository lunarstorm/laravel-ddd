<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Facades\DDD;

trait HasDomainStubs
{
    protected static $usingPublishedStub = false;

    protected function usingPublishedStub($usingPublishedStub = true)
    {
        static::$usingPublishedStub = $usingPublishedStub;

        return $this;
    }

    protected function isUsingPublishedStub(): bool
    {
        return static::$usingPublishedStub;
    }

    protected function getStub()
    {
        $stub = parent::getStub();

        if ($publishedStub = $this->resolvePublishedDddStub($stub)) {
            $stub = $publishedStub;
        }

        $this->usingPublishedStub(str($stub)->startsWith(app()->basePath('stubs')));

        return $stub;
    }

    protected function resolvePublishedDddStub($path)
    {
        $stubFilename = str($path)
            ->basename()
            ->ltrim('/\\')
            ->toString();

        // Check if there is a user-published stub
        if (file_exists($publishedPath = app()->basePath('stubs/ddd/'.$stubFilename))) {
            return $publishedPath;
        }

        // Also check for legacy stub extensions
        if (file_exists($legacyPublishedPath = Str::replaceLast('.stub', '.php.stub', $publishedPath))) {
            return $legacyPublishedPath;
        }

        return null;
    }

    protected function resolveDddStubPath($path)
    {
        $path = str($path)
            ->basename()
            ->ltrim('/\\')
            ->toString();

        if ($publishedPath = $this->resolvePublishedDddStub($path)) {
            return $publishedPath;
        }

        return DDD::packagePath('stubs/'.$path);
    }
}

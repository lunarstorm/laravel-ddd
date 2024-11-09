<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Facades\DDD;

trait HasDomainStubs
{
    protected function getStub()
    {
        $defaultStub = parent::getStub();

        $stubFilename = basename($defaultStub);

        // Check if there is a user-published stub
        if (file_exists($publishedPath = app()->basePath('stubs/ddd/'.$stubFilename))) {
            return $publishedPath;
        }

        // Also check for legacy stub extensions
        if (file_exists($legacyPublishedPath = Str::replaceLast('.stub', '.php.stub', $publishedPath))) {
            return $legacyPublishedPath;
        }

        return $defaultStub;
    }

    protected function resolveDddStubPath($path)
    {
        $path = str($path)
            ->basename()
            ->ltrim('/\\')
            ->toString();

        $publishedPath = resource_path('stubs/ddd/'.$path);

        if (file_exists($publishedPath)) {
            return $publishedPath;
        }

        $legacyPublishedPath = Str::replaceLast('.stub', '.php.stub', $publishedPath);

        if (file_exists($legacyPublishedPath)) {
            return $legacyPublishedPath;
        }

        return DDD::packagePath('stubs/'.$path);
    }

    // protected function resolveStubPath($stub)
    // {
    //     $defaultStub = parent::resolveStubPath($stub);

    //     $stubFilename = basename($stub);

    //     // Check if there is a user-published stub
    //     $publishedPath = app()->basePath('stubs/ddd/'.$stubFilename);

    //     if (file_exists($publishedPath)) {
    //         return $publishedPath;
    //     }

    //     $legacyPublishedPath = Str::replaceLast('.stub', '.php.stub', $publishedPath);

    //     if (file_exists($legacyPublishedPath)) {
    //         return $legacyPublishedPath;
    //     }

    //     return $defaultStub;
    // }
}

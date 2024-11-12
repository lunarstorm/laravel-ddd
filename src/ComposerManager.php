<?php

namespace Lunarstorm\LaravelDDD;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Lunarstorm\LaravelDDD\Support\Path;

class ComposerManager
{
    public readonly string $composerFile;

    protected Composer $composer;

    protected array $data;

    protected ?OutputStyle $output = null;

    public function __construct(?string $composerFile = null)
    {
        $this->composer = app(Composer::class)->setWorkingPath(app()->basePath());

        $this->composerFile = $composerFile ?? app()->basePath('composer.json');

        $this->data = json_decode(file_get_contents($this->composerFile), true);
    }

    public static function make(?string $composerFile = null): self
    {
        return new self($composerFile);
    }

    public function usingOutput(OutputStyle $output)
    {
        $this->output = $output;

        return $this;
    }

    protected function guessAutoloadPathFromNamespace(string $namespace): string
    {
        $rootFolders = [
            'src',
            '',
        ];

        $relativePath = Str::rtrim(Path::fromNamespace($namespace), '/\\');

        foreach ($rootFolders as $folder) {
            $path = Path::join($folder, $relativePath);

            if (is_dir($path)) {
                return $this->normalizePathForComposer($path);
            }
        }

        return $this->normalizePathForComposer("src/{$relativePath}");
    }

    protected function normalizePathForComposer($path): string
    {
        $path = Path::normalize($path);

        return str_replace(['\\', '/'], '/', $path);
    }

    public function hasPsr4Autoload(string $namespace): bool
    {
        return collect($this->getPsr4Namespaces())
            ->hasAny([
                $namespace,
                Str::finish($namespace, '\\'),
            ]);
    }

    public function registerPsr4Autoload(string $namespace, $path)
    {
        $namespace = str($namespace)
            ->rtrim('/\\')
            ->finish('\\')
            ->toString();

        $path = $path ?? $this->guessAutoloadPathFromNamespace($namespace);

        return $this->fill(
            ['autoload', 'psr-4', $namespace],
            $this->normalizePathForComposer($path)
        );
    }

    public function fill($path, $value)
    {
        data_fill($this->data, $path, $value);

        return $this;
    }

    protected function update($set = [], $forget = [])
    {
        foreach ($forget as $key) {
            $this->forget($key);
        }

        foreach ($set as $pair) {
            [$path, $value] = $pair;
            $this->fill($path, $value);
        }

        return $this;
    }

    public function forget($key)
    {
        $keys = Arr::wrap($key);

        foreach ($keys as $key) {
            Arr::forget($this->data, $key);
        }

        return $this;
    }

    public function get($path, $default = null)
    {
        return data_get($this->data, $path, $default);
    }

    public function getPsr4Namespaces()
    {
        return $this->get(['autoload', 'psr-4'], []);
    }

    public function getAutoloadPath($namespace)
    {
        $namespace = Str::finish($namespace, '\\');

        return $this->get(['autoload', 'psr-4', $namespace]);
    }

    public function reload()
    {
        $this->output?->writeLn('Reloading composer (dump-autoload)...');

        $this->composer->dumpAutoloads();

        return $this;
    }

    public function save()
    {
        $this->composer->modify(fn ($composerData) => $this->data);

        return $this;
    }

    public function saveAndReload()
    {
        return $this->save()->reload();
    }

    public function toJson()
    {
        return json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function toArray()
    {
        return $this->data;
    }
}

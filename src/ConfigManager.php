<?php

namespace Lunarstorm\LaravelDDD;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Lunarstorm\LaravelDDD\Facades\DDD;
use Symfony\Component\VarExporter\VarExporter;

class ConfigManager
{
    protected array $packageConfig;

    protected array $config;

    protected string $stub;

    public function __construct(public string $configPath)
    {
        $this->packageConfig = require DDD::packagePath('config/ddd.php');

        $this->config = file_exists($configPath) ? require ($configPath) : $this->packageConfig;

        $this->stub = file_get_contents(DDD::packagePath('config/ddd.php.stub'));
    }

    protected function mergeArray($path, $array)
    {
        $path = Arr::wrap($path);

        $merged = [];

        foreach ($array as $key => $value) {
            $merged[$key] = is_array($value)
                ? $this->mergeArray([...$path, $key], $value)
                : $this->resolve([...$path, $key], $value);
        }

        if (array_is_list($merged)) {
            $merged = array_unique($merged);
        }

        return $merged;
    }

    public function resolve($path, $value)
    {
        $path = Arr::wrap($path);

        return data_get($this->config, $path, $value);
    }

    public function syncWithLatest()
    {
        $fresh = [];

        foreach ($this->packageConfig as $key => $value) {
            $resolved = is_array($value)
                ? $this->mergeArray($key, $value)
                : $this->resolve($key, $value);

            $fresh[$key] = $resolved;
        }

        $this->config = $fresh;

        return $this;
    }

    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return data_get($this->config, $key);
    }

    public function set($key, $value)
    {
        data_set($this->config, $key, $value);

        return $this;
    }

    public function fill($values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function save()
    {
        $content = $this->stub;

        // We will temporary substitute namespace slashes
        // with a placeholder to avoid double exporter
        // escaping them as double backslashes.
        $keysWithNamespaces = [
            'domain_namespace',
            'application.namespace',
            'layers',
            'namespaces',
            'base_model',
            'base_dto',
            'base_view_model',
            'base_action',
        ];

        foreach ($keysWithNamespaces as $key) {
            $value = $this->get($key);

            if (is_string($value)) {
                $value = str_replace('\\', '[[BACKSLASH]]', $value);
            }

            if (is_array($value)) {
                $array = $value;
                foreach ($array as $k => $v) {
                    $array[$k] = str_replace('\\', '[[BACKSLASH]]', $v);
                }
                $value = $array;
            }

            $this->set($key, $value);
        }

        foreach ($this->config as $key => $value) {
            $content = str_replace(
                '{{'.$key.'}}',
                VarExporter::export($value),
                $content
            );
        }

        // Restore namespace slashes
        $content = str_replace('[[BACKSLASH]]', '\\', $content);

        file_put_contents($this->configPath, $content);

        Process::run("./vendor/bin/pint {$this->configPath}");

        return $this;
    }
}

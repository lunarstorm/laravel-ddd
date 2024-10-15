<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;

trait ForwardsToDomainCommands
{
    public function call($command, array $arguments = [])
    {
        $subfolder = Str::contains($this->getNameInput(), '/')
            ? Str::beforeLast($this->getNameInput(), '/')
            : null;

        $nameWithSubfolder = $subfolder ? "{$subfolder}/{$arguments['name']}" : $arguments['name'];

        return match ($command) {
            'make:request' => $this->runCommand('ddd:request', [
                ...$arguments,
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:model' => $this->runCommand('ddd:model', [
                ...$arguments,
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:factory' => $this->runCommand('ddd:factory', [
                ...$arguments,
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:policy' => $this->runCommand('ddd:policy', [
                ...$arguments,
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:migration' => $this->runCommand('ddd:migration', [
                ...$arguments,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:seeder' => $this->runCommand('ddd:seeder', [
                ...$arguments,
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:controller' => $this->runCommand('ddd:controller', [
                ...$arguments,
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            default => $this->runCommand($command, $arguments, $this->output),
        };
    }
}

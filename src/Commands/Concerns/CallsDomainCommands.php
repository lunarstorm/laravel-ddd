<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

use Illuminate\Support\Str;

trait CallsDomainCommands
{
    public function call($command, array $arguments = [])
    {
        $subfolder = Str::contains($this->getNameInput(), '/')
            ? Str::beforeLast($this->getNameInput(), '/')
            : null;

        $nameWithSubfolder = $subfolder ? "{$subfolder}/{$arguments['name']}" : $arguments['name'];

        return match ($command) {
            'make:request' => $this->runCommand('ddd:request', [
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            'make:model' => $this->runCommand('ddd:model', [
                'name' => $nameWithSubfolder,
                '--domain' => $this->domain->dotName,
            ], $this->output),

            default => $this->runCommand($command, $arguments, $this->output),
        };
    }
}

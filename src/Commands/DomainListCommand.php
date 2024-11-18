<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Lunarstorm\LaravelDDD\Support\Domain;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Lunarstorm\LaravelDDD\Support\Path;

use function Laravel\Prompts\table;

class DomainListCommand extends Command
{
    protected $name = 'ddd:list';

    protected $description = 'List all current domains';

    public function handle()
    {
        $headings = ['Domain', 'Namespace', 'Path'];

        $table = collect(DomainResolver::domainChoices())
            ->map(function (string $name) {
                $domain = new Domain($name);

                return [
                    $domain->domain,
                    $domain->layer->namespace,
                    Path::normalize($domain->layer->path),
                ];
            })
            ->toArray();

        table($headings, $table);

        $countDomains = count($table);

        $this->info(trans_choice("{$countDomains} domain|{$countDomains} domains", $countDomains));
    }
}

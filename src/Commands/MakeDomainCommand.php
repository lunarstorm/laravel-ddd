<?php

namespace Lunarstorm\LaravelDDD\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Lunarstorm\LaravelDDD\Support\DomainResolver;
use Symfony\Component\Process\Process;

class MakeDomainCommand extends Command implements PromptsForMissingInput
{
    public $signature = 'ddd:make-domain {domain}';

    protected $description = 'Create a new domain';

    protected string $domain;
    protected mixed $domainStructure;
    protected ?string $domainPath;
    protected ?string $domainRootNamespace;

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'domain' => ['What is the name of the domain?', 'E.g. Payments']
        ];
    }

    public function handle(): int
    {
        $domain = $this->argument('domain');

        if (empty($domain)) {
            $this->error('Domain name is required');
            return self::FAILURE;
        }

        $this->domain = ucfirst($domain);

        $this->domainStructure = config('ddd.make_domain_structure');
        $this->domainPath = DomainResolver::domainPath().'/'.$this->domain;
        $this->domainRootNamespace = DomainResolver::domainRootNamespace().'\\'.$domain;

        $this->createDomainStructure($this->domainStructure);

        return self::SUCCESS;
    }

    protected function createDomainStructure($domainStructure, $currentDir = ''): void
    {
        foreach ($domainStructure as $directory => $content) {
            $this->createDirectoryWithContents($this->domainPath, $directory, $content);
        }

        $this->info('Domain structure created');
    }

    protected function createDirectoryWithContents($path, $directory, $content): void
    {
        $path .= '/' . $directory;
        if (!is_dir($path)) {
            if (! mkdir($path, 0755, true) && ! is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }

        if (empty($content)) {
            return;
        }

        if (is_array($content)) {
            if (isset($content['files']) && is_array($content['files'])) {

                $this->createFiles($path, $content['files']);
                unset($content['files']);
            }

            foreach ($content as $subDirectory => $subContent) {
                $this->createDirectoryWithContents($path, $subDirectory, $subContent);
            }
        }
    }

    protected function createFiles($path, $files): void
    {
        foreach ($files as $file) {
            $this->createFile($path, $file, $this->domainRootNamespace);
        }
    }

    protected function createFile(string $path, string $file, string $namespace): void
    {
        // TODO: Make proper implementation
        $file = $path.'/'.$file.'.php';
        if (!file_exists($file)) {
            $content = file_get_contents(__DIR__.'/../BaseServiceProvider.php');
            file_put_contents($file, $content);
        }
    }
}

<?php

namespace App\Lighthouse;

use Symfony\Component\Process\Process;
use App\Models\Site;
use File;

class LighthouseAuditor
{
    protected const LIGHTHOUSE_PATH = '/var/www/html/node_modules/lighthouse/lighthouse-cli/index.js';
    protected const CHROME_PATH = 'usr/bin/chromium-browser';
    
    protected int $timeout = 500;
    protected array $headers = [];
    protected array $output = ['json'];
    protected array $categories = [];
    protected ?string $nodePath = null;
    protected array $defaultFlags = ['--headless', '--disable-gpu', '--no-sandbox'];
    protected array $outputPath = [];
    protected bool $quiet = true;
    protected ?Process $process = null;
    protected bool $enableDebug = false;

    public function audit(Site $site): self
    {
        $this->process = Process::fromShellCommandline($this->generateCommand($site->url));
        
        $this->process->setTimeout($this->timeout)
                ->run($this->enableDebug ? $this->debugCallback() : null, []);

        if(!$this->process->isSuccessful())
        {
            throw new \Exception($this->process->getErrorOutput());
        }

        return $this;
    }

    public function getAuditOutput(): string
    {
        if(null === $this->process)
        {
            return '';
        }

        return $this->process->getOutput();
    }

    protected function generateCommand(string $url): string
    {
        return implode(' ', [
            'sudo',
            '/root/.nvm/versions/node/v16.13.0/bin/node',
            self::LIGHTHOUSE_PATH,
            $url,
            '--output ' . $this->getOutput(),
            '--plugins=lighthouse-plugin-field-performance',
            !empty($this->outputPath) ? $this->getOutputPath() : null,
            ...$this->headers,
            $this->quiet ? '--quiet' : null,
            $this->getCategories(),
           '--chrome-flags="'.implode(' ', $this->defaultFlags).'"'
        ]);
    }

    public function setCategory(string $category, bool $on):self 
    {
        $catIndex = array_search($category, $this->categories);

        if($catIndex && !$on)
        {
            unset($this->categories[$catIndex]);
        }else if ($on)
        {
            $this->categories[] = $category;
        }

        return $this;
    }


    public function accessibility(bool $on = true): self
    {
        $this->setCategory('accessibility', $on);

        return $this;
    }

    public function bestPractices(bool $on = true): self
    {
        $this->setCategory('best-practices', $on);

        return $this;
    }

    public function performance(bool $on = true): self
    {
        $this->setCategory('performance', $on);

        return $this;
    }

    public function pwa(bool $on = true): self
    {
        $this->setCategory('pwa', $on);

        return $this;
    }

    public function seo(bool $on = true): self
    {
        $this->setCategory('seo', $on);

        return $this;
    }

    public function getCategories(): ?string
    {
        return empty($this->categories) ? null : '--only-categories=' . implode(',', $this->categories);
    }

    public function setHeaders(string $headers): self
    {
        if(empty($headers))
        {
            return $this;
        }

        $headers = json_encode($headers);

        $this->headers = ["--extra-headers" => $headers];

        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function outputCommand($site): string
    {
        return implode(' ', $this->generateCommand($site->url));
    }

    public function outputFormat(string $output = 'json'): self
    {
        $this->output = ['--output', $output];

        return $this;
    }

    public function getOutput(): string
    {
        return implode(' ', $this->output);
    }

    public function outputPath(string $path = "./report.json") : self
    {
        $this->outputPath = ['--output-path=', $path];

        return $this;
    }

    public function getOutputPath(): string
    {
        return implode('', $this->outputPath);
    }

    public function quite(bool $value): self
    {
        $this->quiet = $value;

        return $this;
    }

    public function enableDebugLogs(bool $value): self
    {
        $this->enableDebug = $value;

        return $this;
    }

    private function debugCallback (): callback
    {
        return function ($type, $buffer) {
            $logType = Process::ERR === $type ? 'ERR > ' : 'OUT >';
            dump($logType . $buffer);
        };
    }
}

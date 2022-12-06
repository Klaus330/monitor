<?php

namespace App\Lighthouse;

use Symfony\Component\Process\Process;
use File;

class LighthouseAuditor
{
    protected $timeout = 120;
    protected $headers = [];
    protected $lighthousePath = './node_modules/lighthouse/lighthouse-cli/index.js';
    protected $output = ['json'];
    protected $outputFormat = '--output=json';
    protected $categories = [];
    protected $nodePath = null;
    protected $defaultFlags = ['--headless', '--disable-gpu', '--no-sandbox'];
    protected $outputPath = [];


    public function __construct()
    {
        //
    }

    public function audit($site)
    {
        dd(\TitasGailius\Terminal\Terminal::run('node -V')->getOutput());

        // dd($this->generateCommand($site->url));
        $process = new Process($this->generateCommand($site->url));

        $process->setTimeout($this->timeout)
                ->run(callback: null, env: []);

        if(!$process->isSuccessful())
        {
            throw new \Exception($process->getErrorOutput());
        }

        dd($process->getOutput());
        return $process->getOutput();
    }

    public function generateCommand($url)
    {
        return [
            'echo $PATH',
        //     // '/root/.nvm/versions/node/v16.13.0/bin/node',
        //     $this->lighthousePath,
        //     $url,
        //     '--output ' . $this->getOutput(),
        //     !empty($this->outputPath) ? $this->getOutputPath() : null,
        //     ...$this->headers,
        //     '--quite',
        //     $this->getCategories(),
        //    '--chrome-flags="'.implode(' ', $this->defaultFlags).'"'
        ];
    }

    public function setCategory($category, $on)
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


    public function accessibility($on = true)
    {
        $this->setCategory('accessibility', $on);

        return $this;
    }

    public function bestPractices($on = true)
    {
        $this->setCategory('best-practices', $on);
        
        return $this;
    }

    public function performance($on = true)
    {
        $this->setCategory('performance', $on);
        
        return $this;
    }

    public function pwa($on = true)
    {
        $this->setCategory('pwa', $on);
        
        return $this;
    }

    public function seo($on = true)
    {
        $this->setCategory('seo', $on);
        
        return $this;
    }

    public function getCategories()
    {
        return empty($this->categories) ? null : '--only-categories=' . implode(',', $this->categories);
    }

    public function setHeaders($headers)
    {
        if(empty($headers))
        {
            return $this;
        }

        $headers = json_encode($headers);

        $this->headers = ["--extra-headers" => $headers];

        return $this;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function outputCommand($site)
    {
        return implode(' ', $this->generateCommand($site->url));
    }

    public function outputFormat($output = 'json')
    {
        $this->output = ['--output', $output];

        return $this;
    }

    public function getOutput()
    {
        return implode(' ', $this->output);
    }

    public function outputPath($path = "./report.json")
    {
        $this->outputPath = ['--output-path=', $path];

        return $this;
    }

    public function getOutputPath()
    {
        return implode('', $this->outputPath);
    }
}
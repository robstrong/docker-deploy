<?php

namespace Strong\Deploy;

use Symfony\Component\Process\Process;

class ImageBuilder
{
    protected $cachePath;
    protected $repoAddr;
    protected $branch;
    protected $token;

    public function __construct($repoAddr, $branch, $token)
    {
        $this->setAddress($repoAddr);
        $this->setBranch($branch);
        $this->setGithubToken($token);
    }

    protected function cloneRepository(
    {
        if (!$this->repoExists()) {
            $this->runCommand('git clone https://' . $this->getToken() . ':x-oauth-basic@' . $this->getAddress());
        }

        //checkout correct branch
        $this->runCommand('git fetch');
        $this->runCommand('git checkout -f ' . $this->getBranch());
        $this->runCommand('git reset --hard origin/' . $this->getBranch());

    }

    protected function runCommand($cmd)
    {
        $process = new Process($cmd);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Error, command: ' . $cmd . "\nProduced Error: " . $process->getErrorOutput());
        }
    }

    public function repoExists()
    {
        if (is_dir($this->getCachePath() . $this->getAddress())) {
            return true;
        }
        return false;
    }

    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function setCachePath($path)
    {
        $path = rtrim($path, '/') . '/';
        $this->cachePath = $path;
    }

    public function setAddress($addr)
    {
        $this->repoAddr = $addr;
        return $this;
    }

    public function getAddress()
    {
        return $this->repoAddr;
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    public function getBranch()
    {
        return $this->branch;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }


    public function getToken()
    {
        return $this->token;
    }

    public function build()
    {
        $this->cloneRepository();
        

    }
}

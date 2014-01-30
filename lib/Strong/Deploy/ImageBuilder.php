<?php

namespace Strong\Deploy;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class ImageBuilder
{
    protected $cachePath;
    protected $repoAddr;
    protected $branch;
    protected $token;
    protected $config = array();

    public function __construct($repoAddr, $branch, $token, Phocker\Docker $docker = null)
    {
        $this->setAddress($repoAddr);
        $this->setBranch($branch);
        $this->setToken($token);
        $this->setCachePath(storage_path() . '/repos');
        if (empty($docker)) {
            $docker = new \Strong\Phocker\Docker;
        }
        $this->setDocker($docker);
    }

    protected function cloneRepository()
    {
        if (!$this->repoExists()) {
            $this->runCommand(
                'git clone https://' . $this->getToken() . ':x-oauth-basic@' . $this->getAddress() .
                ' ' . $this->getRepoPath()
            );
        }

        //checkout correct branch
        $this->runCommand('git fetch', $this->getRepoPath());
        $this->runCommand('git checkout -f ' . $this->getBranch(), $this->getRepoPath());
        $this->runCommand('git reset --hard origin/' . $this->getBranch(), $this->getRepoPath());

        $this->fetchConfig();
    }

    protected function fetchConfig()
    {
        $config = array();
        if (is_file($this->getRepoPath() . 'build/config.yml')) {
            $config = file_get_contents($this->getRepoPath() . 'build/config.yml');
            $config = Yaml::parse($config);
            $this->config = new Config($config);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRepoPath()
    {
        return $this->getCachePath() . $this->getAddress() . '/';
    }

    protected function runCommand($cmd, $cwd = null)
    {
        $process = new Process($cmd, $cwd);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Error, command: ' . $cmd . "\nProduced Error: " . $process->getErrorOutput());
        }
        return $process->getOutput();
    }

    public function repoExists()
    {
        if (is_dir($this->getRepoPath())) {
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

    public function getDocker()
    {
        return $this->docker;
    }

    public function setDocker(\Strong\Phocker\Docker $docker)
    {
        $this->docker = $docker;
        return $this;
    }

    public function build()
    {
        $this->cloneRepository();

        //package the dockerfile into a tar
        $tar = $this->runCommand('tar c .', $this->getRepoPath() . 'build/');
        $nameHash = md5($tar);
        if (!$this->getDocker()->imageExists($nameHash)) {
            $this->getDocker()->build($tar, $nameHash);
        }
        return $nameHash;
    }
}

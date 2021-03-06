<?php

namespace Strong\Deploy;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class ImageBuilder
{
    protected $cachePath;
    protected $repo;
    protected $branch;
    protected $config;

    public function __construct(\Repository $repo, $branch, Phocker\Docker $docker = null)
    {
        $this->setRepo($repo);
        $this->setBranch($branch);
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
                'git clone https://' . $this->getRepo()->token . ':x-oauth-basic@' . $this->getRepo()->address .
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
        }
        $this->config = new Config($config);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRepoPath()
    {
        return $this->getCachePath() . $this->getRepo()->address . '/';
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

    public function getRepo()
    {
        return $this->repo;
    }

    public function setRepo(\Repository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    public function getBranch()
    {
        return $this->branch;
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
        $imageName = $this->getRepo()->owner . '/' . $this->getRepo()->name . ':' . $nameHash;
        if (!$this->getDocker()->imageExists($imageName)) {
            $this->getDocker()->build(
                $tar, 
                $imageName
            );
        }
        return $imageName;
    }
}

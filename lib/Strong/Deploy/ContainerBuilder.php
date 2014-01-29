<?php

namespace Strong\Deploy;

class ContainerBuilder
{
    protected $clonePath = '/var/www';

    public function __construct(\Site $site, array $config)
    {
        $this->site = $site;
        $this->config = $config;
    }

    public function build()
    {
        $docker = new \Strong\Phocker\Docker;
        $container = $docker->createContainer($this->site->tag);
        $docker->startContainer($container->Id);
        sleep(2); //wait a second for the container to start

        //clone repo into container
        $containerInfo = $docker->inspectContainer($container->Id);
        $containerModel = new \Container();
        $containerModel->docker_id = $container->Id;
        $this->site->containers()->save($containerModel);

        $this->setIp($containerInfo->NetworkSettings->IPAddress);
        $this->setupRepo($this->getIp());

    }

    protected function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setupRepo($ip)
    {
        $ssh = new \Strong\Ssh(
            array(
                'host'      => $ip,
                'user'      => 'root',
                'password'  => 'pica9'
            )
        );
        $this->installRepo($ssh);
        $this->installComposer($ssh);
        $this->runPostCloneCommands($ssh);
    }

    protected function installRepo($ssh)
    {
        $git = new \Strong\SourceControl;
        $git->setRepository('git@github.com:' . $this->site->repository->owner . '/' . $this->site->repository->name . '.git')
            ->setCommit($this->site->branch)
            ->setClonePath($this->clonePath)
            ->setGithubToken($this->site->repository->token())
            ->setSshConnection($ssh)
            ->setupRepository();
    }

    protected function installComposer($ssh)
    {
        //if composer.json exists, run composer install
        if ($ssh->fileExists($this->clonePath . '/composer.json')) {
            $ssh->runCommand('cd ' . $this->clonePath . ' && composer install');
        }
    }

    protected function runPostCloneCommands($ssh)
    {
        foreach ($this->config['post_clone_cmd'] as $cmd) {
            $ssh->runCommand("cd " . $this->clonePath . " && " . $cmd);
        }
    }
}

<?php

namespace Strong\Deploy;

class ContainerBuilder
{
    protected $clonePath = '/var/www';

    public function __construct(\Site $site, Config $config)
    {
        $this->site = $site;
        $this->config = $config;
    }

    public function build()
    {
        $links = $this->buildAddons();
        $this->buildPrimaryInstance($links);
    }

    protected function buildPrimaryInstance($links = array())
    {
        $docker = new \Strong\Phocker\Docker;
        $container = $docker->createContainer($this->site->tag, str_replace('.', '', $this->site->getFullUrl()));
        $docker->startContainer($container->Id, $links);
        sleep(2); //wait a second for the container to start

        //save container info to DB
        $containerModel = new \Container();
        $containerModel->docker_id = $container->Id;
        $containerModel->name = $this->site->getFullUrl();
        $this->site->containers()->save($containerModel);

        //clone repo into container
        $containerInfo = $docker->inspectContainer($container->Id);
        $this->setIp($containerInfo->NetworkSettings->IPAddress);
        $this->setupRepo($this->getIp());
    }

    protected function buildAddons()
    {
        $docker = new \Strong\Phocker\Docker;
        $links = array();
        foreach ($this->config->getAddons() as $type => $properties) {
            //don't build the instance if it already exists
            try {
                $addon = \Container::addon($properties['name'])->firstOrFail();
                $links[] = $properties['name'] . ':' . $properties['env_name'];
                continue;
            } catch (\Exception $e) {
                switch ($type) {
                    case "redis":
                        $imageName = "dockerfile/redis";
                        break;
                    case "postgres":
                        $imageName = "postgres";
                        break;
                }
                $container = $docker->createContainer($imageName, $properties['name']);
                $docker->startContainer($container->Id);
                $containerInfo = $docker->inspectContainer($container->Id);
                $containerModel = new \Container();
                $containerModel->docker_id = $container->Id;
                $containerModel->addon_type = $type;
                $containerModel->name = $properties['name'];
                $containerModel->save();
                $links[] = $properties['name'] . ':' . $properties['env_name'];
            }
        }
        return $links;
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
        foreach ($this->config->get('post_clone_cmd') as $cmd) {
            $ssh->runCommand("cd " . $this->clonePath . " && " . $cmd);
        }
    }
}

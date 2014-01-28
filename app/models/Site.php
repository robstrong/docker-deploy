<?php

use Strong\Deploy\ImageBuilder;

class Site extends Eloquent
{
    protected $table = 'sites';

    public static function boot()
    {
        parent::boot();

        Site::created(function($site) {
            //queue image build
            Queue::push(
                'Strong\Deploy\Queue\CreateInstance', 
                array(
                    'site_id'           => $site->id,
                    'start_container'   => true
                )
            );
        });

        Site::deleting(function($site) {
            $site->clearProxyEntry();
            $site->destroyContainers();
        });
    }

    public function repository()
    {
        return $this->belongsTo('Repository');
    }

    public function domain()
    {
        return $this->belongsTo('Domain');
    }

    public function containers()
    {
        return $this->hasMany('Container');
    }

    public function buildImage()
    {
        $repo = $this->repository;
        $builder = new ImageBuilder(
            'github.com/' . $repo->owner . '/' . $repo->name . '.git',
            $this->branch,
            $repo->token()
        );
        $tag = $builder->build();
        $this->tag = $tag;
        $this->save();
    }

    public function startContainer()
    {
        $docker = new \Strong\Phocker\Docker;
        $container = $docker->createContainer($this->tag);
        $docker->startContainer($container->Id);
        sleep(2); //wait a second for the container to start

        //clone repo into container
        $containerInfo = $docker->inspectContainer($container->Id);
        $containerModel = new Container();
        $containerModel->docker_id = $container->Id;
        $this->containers()->save($containerModel);
        $ip = $containerInfo->NetworkSettings->IPAddress;
        $ssh = new \Strong\Ssh(
            array(
                'host'      => $ip,
                'user'      => 'root',
                'password'  => 'pica9'
            )
        );
        
        $git = new \Strong\SourceControl;
        $git->setRepository('git@github.com:' . $this->repository->owner . '/' . $this->repository->name . '.git')
            ->setCommit($this->branch)
            ->setClonePath('/var/www')
            ->setGithubToken($this->repository->token())
            ->setSshConnection($ssh)
            ->setupRepository();

        $this->addProxyEntry($ip);

        return $container->Id;
    }

    public function getFullUrl()
    {
        $url = '';
        if (!empty($this->subdomain)) {
            $url = trim($this->subdomain, '.') . '.';
        }
        $url .= $this->domain->domain;

        return $url;
    }

    public function addProxyEntry($ip)
    {
        Redis::connection()->set($this->getFullUrl(), $ip);

    }

    public function clearProxyEntry()
    {
        Redis::connection()->del($this->getFullUrl());
    }

    public function destroyContainers()
    {
        foreach ($this->containers as $container) {
            $container->delete();
        }
    }
}

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
    }

    public function repository()
    {
        return $this->belongsTo('Repository');
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

        //set host in redis
        Redis::connection()->set($this->url, $ip);

        return $container->Id;
    }
}

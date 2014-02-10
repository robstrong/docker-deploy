<?php

use Strong\Deploy\ImageBuilder;
use Strong\Deploy\ContainerBuilder;

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

    public function aliases()
    {
        return $this->hasMany('Alias');
    }

    public function startInstance()
    {
        $config = $this->buildImage();
        $this->createContainer($config);
    }

    public function buildImage()
    {
        $repo = $this->repository;
        $builder = new ImageBuilder($repo, $this->branch);
        $tag = $builder->build();
        $this->tag = $tag;
        $this->save();
        return $builder->getConfig();
    }

    public function createContainer(\Strong\Deploy\Config $config)
    {
        $builder = new ContainerBuilder($this, $config);
        $container = $builder->build();
        $this->registerContainerWithProxy($container);
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

    protected function registerContainerWithProxy($newContainer)
    {
        //switch proxy
        $this->addProxyEntry($newContainer->ip);

        //set alias entries
        foreach ($this->aliases as $alias) {
            $alias->addProxyEntry($newContainer->ip);
        }

        //delete old ones
        foreach ($this->containers as $container) {
            if ($container->id != $newContainer->id) {
                $container->delete();
            }
        }
    }

    protected function addProxyEntry($ip)
    {
        Redis::connection()->set($this->getFullUrl(), $ip);
    }

    protected function clearProxyEntry()
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

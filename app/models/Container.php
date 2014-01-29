<?php

class Container extends Eloquent
{
    protected $table = 'containers';

    public static function boot()
    {
        parent::boot();

        Container::deleting(function($container) {
            $docker = new \Strong\Phocker\Docker;
            $docker->kill($container->docker_id);
            $docker->deleteContainer($container->docker_id);
        });
    }
}

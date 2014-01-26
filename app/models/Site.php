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
            Queue:push(
                'Strong\Deploy\Queue\BuildImage', 
                array(
                    'site_id'           => $this->id,
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
        $repo = $this->repository();
        $builder = new ImageBuilder(
            'github.com/' . $repo->owner . '/' . $repo->name . '.git',
            $site->branch,
            $repo->token()
        );
        $builder->build();
    }

}

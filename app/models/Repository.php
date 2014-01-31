<?php

class Repository extends Eloquent
{
    protected $table = 'repositories';

    public function sites()
    {
        return $this->hasMany('Site');
    }

    public function user()
    {
        return $this->belongsTo('User', 'auth_user_id');
    }

    public function getTokenAttribute()
    {
        return $this->user->getGithubToken();
    }

    public function getAddressAttribute()
    {
        return 'github.com/' . $this->owner . '/' . $this->name . '.git';
    }

    public function getRepositoryAttribute()
    {
        return $this->owner . '/' . $this->name;
    }

    public function installGithubPushHook()
    {
        //get github api with this repo's user auth'd
        $github = new \Github\Client(
            new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
        );
        $github->authenticate($this->user->getGithubToken(), null, \Github\Client::AUTH_HTTP_TOKEN);

        //check if hook already exists
        $hooks = $github->api('repo')->hooks()->all($this->owner, $this->name);
        $exists = false;
        foreach ($hooks as $hook) {
            if ($hook['config']['url'] == 'http://zunction.com/github/hooks') {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $github->api('repo')->hooks()->create(
                $this->owner, 
                $this->name,
                array(
                    'name'      => 'web',
                    'config'    => array(
                        'url'           => 'http://zunction.com/github/hooks',
                        'content_type'  => 'form',
                        'insecure_ssl'  => '1',
                    )
                )
            );
        }
    }
}


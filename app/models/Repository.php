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

    public function token()
    {
        return $this->user->getGithubToken();
    }
}


<?php

class Token extends Eloquent
{
	protected $table = 'tokens';

    public function scopeGithub($query)
    {
        return $query->where('authorization_server', '=', 'api.github.com');
    }
}

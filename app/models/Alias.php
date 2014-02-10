<?php

class Alias extends Eloquent
{
    public static function boot()
    {
        parent::boot();

        Alias::deleting(function($alias) {
            $alias->clearProxyEntry();
        });
    }

    public function site()
    {
        return $this->belongsTo('Site');
    }

    public function domain()
    {
        return $this->belongsTo('Domain');
    }

    public function addProxyEntry($ip)
    {
        Redis::connection()->set($this->getFullUrl(), $ip);
    }

    public function clearProxyEntry()
    {
        Redis::connection()->del($this->getFullUrl());
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
}

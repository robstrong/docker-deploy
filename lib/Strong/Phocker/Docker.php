<?php

namespace Strong\Phocker;

class Docker
{
    public function __construct($host)
    {
        $this->host = $host;
    }

    public function create($obj)
    {
        //if image object, create image
        //if container object create container
    }
}

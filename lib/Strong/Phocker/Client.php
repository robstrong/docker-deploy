<?php

namespace Strong\Phocker;

class Client
{
    protected $host;
    protected $port;
    protected $stream;

    public function __construct($host = '127.0.0.1', $port = 4243)
    {
        $this->setHost($host);
        $this->setPort($port);
    }

    public function setHttpClient($client)
    {
        $this->client = $client;
        return $this;
    }

    public function getHttpClient()
    {
        if (empty($this->client)) {
            $this->client = new \Guzzle\Http\Client('http://' . $this->getHost() . ':' . $this->getPort());
        }
        return $this->client;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function post($endpoint, $body, $headers = array())
    {
        $req = $this->getHttpClient()->post($endpoint, $headers, $body);
        return $req->send();
    }

    public function get($endpoint)
    {
        $req = $this->getHttpClient()->get($endpoint);
        return $req->send();
    }

    public function delete($endpoint)
    {
        $req = $this->getHttpClient()->delete($endpoint);
        return $req->send();
    }
}

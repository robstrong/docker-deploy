<?php

namespace Strong\Phocker;

class Docker
{
    public function __construct(Client $client = null)
    {
        if (empty($client)) {
            $client = new Client();
        }
        $this->setClient($client);
    }

    public function build($tar, $name = null, $noCache = false)
    {
        $queryParams = array('q' => 1);
        if (!empty($name)) {
            $queryParams['t'] = $name;
        }

        if ($noCache) {
            $queryParams['nocache'] = 1;
        }

        $resp = $this->getClient()->post('/build?' . http_build_query($queryParams), $tar);
        if ($resp->getStatusCode() != 200) {
            throw new \Exception('(' . $resp->getStatusCode() . ') ' . $resp->getBody());
        }
    }

    public function createContainer($image)
    {
        $config = array(
            "Image"         => $image,
        );

        $resp = $this->getClient()->post('/containers/create', json_encode($config), array('Content-Type' => 'application/json'));
        if ($resp->getStatusCode() != 201) {
            throw new \Exception('(' . $resp->getStatusCode() . ') ' . $resp->getBody());
        }
        return json_decode($resp->getBody());
    }

    public function startContainer($id)
    {
        $config = array(
            "PublishAllPorts"   => false,
            "Privileged"        => false,
        );
        $resp = $this->getClient()->post('/containers/' . $id . '/start', json_encode($config), array('Content-Type' => 'application/json'));
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function imageExists($name)
    {
        try {
            $resp = $this->getClient()->get('/images/' . $name . '/json');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function inspectContainer($id)
    {
        $resp = $this->getClient()->get('/containers/' . $id . '/json');
        return json_decode($resp->getBody());
    }

}

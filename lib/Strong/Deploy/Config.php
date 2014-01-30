<?php

namespace Strong\Deploy;

use Symfony\Component\Yaml\Yaml;

class Config
{
    protected $configData;
    protected $config;
    protected $defaultConfig = array(
        'clone_dir'             => '/var/www',
        'post_clone_cmd'        => array(),
        'os'                    => 'centos-6.4',
        'php'                   => '5.4',
        'webserver'             => 'apache',
        'misc_bin'              => array(),
        'addons'                => array(),
    );

    public function __construct(array $configData = null)
    {
        if ($configData) {
            $this->setConfig($configData);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($data)
    {
        $this->config = $this->parseConfig($data);
        return $this;
    }

    public function parseConfig(array $config = null)
    {
        $config = array_merge($this->defaultConfig, $config);
        $this->validateAddons($config['addons']);
        return $config;
    }

    public function get($value)
    {
        $path = explode(".", $value);
        $value = $this->config;
        foreach ($path as $key) {
            if (!is_array($value) || !isset($value[$key])) {
                throw new \Exception('Invalid config key: ' . $key);
            }
            $value = $value[$key];
        }
        return $value;
    }

    public function getAddons()
    {
        return $this->config['addons'];
    }

    protected function validateAddons($addons)
    {
        if (!is_array($addons)) {
            $addons = array($addons);
        }

        foreach ($addons as $type => $addon) {
            if (!isset($addon['name'])) {
                throw new \Exception('Name is not set for addon of type: ' . $type);
            }
        }
        return true;
    }
}

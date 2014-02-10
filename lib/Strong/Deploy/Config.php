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
        'php'                   => array(
            'version'           => '5.4',
            'extensions'        => array(),
        ),
        'webserver'             => array(
            'apache'    => array(
                'public_dir'    => 'public'
            )
        ),
        'misc_bin'              => array(),
        'addons'                => array(),
    );

    public function __construct(array $configData = array())
    {
        $this->setConfig($configData);
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
        $config = array_merge_recursive($this->defaultConfig, $config);
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

    public function getWebserver()
    {
        if (!isset($this->config['webserver'])) {
            return "apache";
        }
        return key($this->config['webserver']);
    }

    public function getAddons()
    {
        if (!isset($this->config['addons'])) {
            return array();
        }
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

<?php

namespace Strong\Deploy\Dockerfile;

/*
 * This will build a directory with a Dockerfile and all of the scripts
 * required to build the Docker image
 */

class DockerfileBuilder
{
    protected $config;
    protected $defaultScriptPath = '/home/rstrong/deploy/docker/';
    protected $dockerRootPath = '/root/docker/';

    //relative to dockerRootPath
    protected $containerScriptPath = 'scripts/';
    protected $outputPath;

    public function __construct(\Strong\Deploy\Config $config)
    {
        $this->config = $config;
    }

    public function setOutputPath($dir)
    {
        $this->outputPath = $dir;
        return $this;
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

    public function build()
    {
        //copy default scripts to output path
        $this->copyDir($this->defaultScriptPath, $this->getOutputPath());

        //write generated Dockerfile to output path
        file_put_contents($this->getOutputPath() . 'Dockerfile', $this->getDockerfile());
    }

    protected function copyDir($src, $dest)
    {
        $dir = opendir($src); 
        if (!is_dir($dest)) {
            mkdir($dest);
        }
        while (($file = readdir($dir)) !== false) { 
            if (!in_array($file, array('.', '..'))) {
                if (is_dir($src . '/' . $file)) { 
                    $this->copyDir($src . '/' . $file, $dest . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file, $dest . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }

    protected function getDockerfile()
    {
        $dockerStr = '';
        $dockerStr .= $this->getAddScripts();
        $dockerStr .= $this->getBaseImage();
        $dockerStr .= $this->getOsScripts();
        $dockerStr .= $this->getWebserverScripts();
        $dockerStr .= $this->getPhpScripts();
        $dockerStr .= $this->getBinScripts();
        return $dockerStr;
    }

    protected function getScriptPath($path)
    {
        return $this->containerScriptPath;
    }

    protected function setScriptPath()
    {
        $this->containerScriptPath = $this->getDockerRoot() . $path;
        return $this;
    }

    protected function getDockerRootPath()
    {
        return $this->dockerRootPath;
    }

    protected function setDockerRootPath($path)
    {
        $this->dockerRootPath = rtrim($path, "/") . "/";
        return $this;
    }

    protected function getAddScripts()
    {
        return "ADD files " . $this->getScriptPath();
    }

    protected function getBaseImage()
    {
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                $image = "centos:6.4";
                break;
        }

        return "FROM " . $image;
    }

    protected function getOsScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                $cmds[] = "RUN " . $this->getScriptPath() . "centos/repositories/epel.sh\n";
                $cmds[] = "RUN " . $this->getScriptPath() . "centos/common/os.sh\n";
                break;
        }

        return implode("\n", $cmds);
    }

    protected function getWebserverScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->getWebserver()) {
            case "apache":
                $cmds[] = "RUN " . $this->getScriptPath() . "apache/install.sh\n";
                break;
        }

        return implode("\n", $cmds);
    }

    protected function getPhpScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                switch ($this->getConfig()->get('php.version')) {
                    case "5.4":
                        $cmds[] = "RUN " . $this->getScriptPath() . "centos/repositories/remi.sh\n";
                        $cmds[] = "RUN " . $this->getScriptPath() . "centos/php/php-5.4.sh\n";
                        break;
                }
        }

        $cmds .= "\n" . $this->getPhpExtensions();

        return implode("\n", $cmds);
    }

    protected function getPhpExtensions()
    {
        $cmds = array();
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                switch ($this->getConfig()->get('php.extensions')) {
                    case "yaml":
                        $cmds[] = "RUN " . $this->getScriptPath() . "centos/php/php-yaml.sh\n";
                        break;
                }
        }

        return implode("\n", $cmds);
    }

    protected function getBinScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                switch ($this->getConfig()->get('misc_bin')) {
                    case "sass":
                        $cmds[] = "RUN " . $this->getScriptPath() . "centos/sass.sh\n";
                        break;
                }
        }

        return implode("\n", $cmds);
    }
}

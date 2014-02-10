<?php

namespace Strong\Deploy\Dockerfile;

/*
 * This will build a directory with a Dockerfile and all of the scripts
 * required to build the Docker image
 */

class Builder
{
    protected $config;
    protected $defaultScriptPath = '/home/rstrong/deploy/docker/';
    protected $dockerRootPath = '/root/docker/';
    protected $cmds = array();

    //relative to dockerRootPath
    protected $containerScriptPath = 'scripts/';
    protected $outputPath;

    public function __construct(\Strong\Deploy\Config $config)
    {
        $this->config = $config;
    }

    public function __destruct()
    {
        $this->deleteOutputDir();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getOutputPath()
    {
        if (!isset($this->outputPath)) {
            $this->outputPath = $this->getTempOutputPath();
        }
        return $this->outputPath;
    }

    protected function getTempOutputPath()
    {
        $tmpPath = sys_get_temp_dir();

        do {
            $dir = sys_get_temp_dir() . '/' . $this->getRandomName() . '/';
        } while(is_dir($dir));

        mkdir($dir);
        return $dir;
    }

    protected function getRandomName()
    {
        return 'DOCKERFILEBUILDER' . rand(1000, 10000);
    }

    public function deleteOutputDir()
    {
        die('delete dir: ' . $this->getOutputPath());
        if (is_dir($this->getOutputPath())) {
            $this->deleteDir($this->getOutputPath());
        }
    }

    protected function deleteDir($path)
    {
        if(!file_exists($path)) {
            throw new RecursiveDirectoryException('Directory doesn\'t exist.');
        }

        $directoryIterator = new DirectoryIterator($path);

        foreach($directoryIterator as $fileInfo) {
            $filePath = $fileInfo->getPathname();
            if(!$fileInfo->isDot()) {
                if($fileInfo->isFile()) {
                    unlink($filePath);
                } elseif($fileInfo->isDir()) {
                    if($this->emptyDirectory($filePath)) {
                        rmdir($filePath);
                    } else {
                        $this->deleteDir($filePath);
                    }
                }
            }
        }
        rmdir($path);
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
                } else { 
                    copy($src . '/' . $file, $dest . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }

    protected function getDockerfile()
    {
        $this->addBaseImage()
            ->addAddScripts()
            ->addOsScripts()
            ->addWebserverScripts()
            ->addPhpScripts()
            ->addBinScripts();
        return implode("\n", $this->cmds);
    }

    protected function addDockerCmd($cmd)
    {
        if (is_array($cmd)) {
            $this->cmds = array_merge($this->cmds, $cmd);
        } else {
            $this->cmds[] = $cmd;
        }
        return $this;
    }

    protected function getScriptPath()
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

    protected function addAddScripts()
    {
        $this->addDockerCmd("ADD files " . $this->getScriptPath());
        return $this;
    }

    protected function addBaseImage()
    {
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                $image = "centos:6.4";
                break;
        }

        $this->addDockerCmd("FROM " . $image);
        return $this;
    }

    protected function addOsScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                $cmds[] = "RUN " . $this->getScriptPath() . "centos/repositories/epel.sh";
                $cmds[] = "RUN " . $this->getScriptPath() . "centos/common/os.sh";
                break;
        }

        $this->addDockerCmd($cmds);
        return $this;
    }

    protected function addWebserverScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->getWebserver()) {
            case "apache":
                $cmds[] = "RUN " . $this->getScriptPath() . "apache/install.sh";
                break;
        }

        $this->addDockerCmd($cmds);
        return $this;
    }

    protected function addPhpScripts()
    {
        $cmds = array();
        switch ($this->getConfig()->get('os')) {
            case "centos-6.4":
                switch ($this->getConfig()->get('php.version')) {
                    case "5.4":
                        $cmds[] = "RUN " . $this->getScriptPath() . "centos/repositories/remi.sh";
                        $cmds[] = "RUN " . $this->getScriptPath() . "centos/php/php-5.4.sh";
                        break;
                }
        }

        $this->addDockerCmd($cmds);
        $this->addPhpExtensions();
        return $this;
    }

    protected function addPhpExtensions()
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

        $this->addDockerCmd($cmds);
        return $this;
    }

    protected function addBinScripts()
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

        $this->addDockerCmd($cmds);
        return $this;
    }
}

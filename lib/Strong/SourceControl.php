<?php

namespace Strong;

use GitWrapper\GitWrapper;
use Illuminate\Filesystem\Filesystem;

class SourceControl
{
    protected $repo;
    protected $commit;
    protected $repoPath;
    protected $ssh;
    protected $privateKey = false;
    protected $githubToken = false;

    public function setRepository($repo)
    {
        $this->repo = $repo;
        return $this;
    }

    public function setCommit($hash)
    {
        $this->commit = $hash;
        return $this;
    }

    public function setClonePath($dir)
    {
        $this->repoPath = $dir;
        return $this;
    }

    public function setSshConnection($ssh)
    {
        $this->ssh = $ssh;
        return $this;
    }

    public function setupRepository()
    {
        $this->cleanPath();
        $this->cloneRepo();
    }

    public function setPrivateKey($key)
    {
        $this->privateKey = $key;
        return $this;
    }

    public function setGithubToken($token)
    {
        $this->githubToken = $token;
        return $this;
    }

    public function cleanPath()
    {
        if (empty($this->repoPath)) {
            throw new \Exception('Invalid repository path: ' . $this->repoPath);
        }

        $this->ssh->runCommand('rm -Rf ' . $this->repoPath . '/*');
    }

    public function cloneRepo()
    {
        $repo = $this->repo;
        if ($this->githubToken) {
            $repo = $this->getTokenRepo();
        } elseif ($this->privateKey) {
            $this->sendPrivateKey();
        }

        $this->ssh->runCommand('git clone ' . $repo . ' ' . $this->repoPath);
        $this->ssh->runCommand('cd ' . $this->repoPath . ' && git reset --hard ' . $this->commit);
    }

    protected function getTokenRepo()
    {
        return str_replace("git@github.com:", "https://" . $this->githubToken . ":x-oauth-basic@github.com/", $this->repo);
    }

    public function sendPrivateKey()
    {
        $privateKeyFile = tmpfile();
        fwrite($privateKeyFile, $this->privateKey);
        $fileInfo = stream_get_meta_data($privateKeyFile);
        if (!$this->ssh->sendFile($fileInfo['uri'], '/root/.ssh/id_rsa', 0600)) {
            fclose($privateKeyFile);
            throw new \Exception('Failed to transfer private key');
        }
        fclose($privateKeyFile);
    }
}

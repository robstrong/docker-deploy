<?php

namespace Strong;

class Ssh
{
    protected $connection;
    protected $defaultConnectionInfo = array(
        'port'  => 22,
    );

    public function __construct($connectionInfo)
    {
        $connectionInfo = array_merge($this->defaultConnectionInfo, $connectionInfo);

        if (!($this->connection = ssh2_connect($connectionInfo['host'], $connectionInfo['port']))) { 
            throw new \Exception('Cannot connect to server'); 
        } 

        if (!ssh2_auth_password($this->connection, $connectionInfo['user'], $connectionInfo['password'])) { 
            throw new \Exception('Authentication rejected by server'); 
        } 
    }

    public function runCommand($cmd)
    {
        $stream = ssh2_exec($this->connection, $cmd);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);

        $output = stream_get_contents($stream);
        $errorOutput = stream_get_contents($errorStream);

        fclose($errorStream);
        fclose($stream);

        if (!empty($errorOutput)) {
            throw new \Exception('Error running command: ' . $errorOutput);
        }

        return $output;
    }

    public function sendFile($localFile, $remoteFile, $mode = 0644)
    {
        return ssh2_scp_send($this->connection, $localFile, $remoteFile, $mode);
    }

    public function receiveFile($remoteFile, $localFile)
    {
        return ssh2_scp_recv($this->connection, $remoteFile, $localFile);
    }
}

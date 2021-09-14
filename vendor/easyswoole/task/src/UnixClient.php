<?php


namespace EasySwoole\Task;


use Swoole\Coroutine\Client;

class UnixClient
{
    private $client = null;

    function __construct(string $unixSock,$maxSize)
    {
        $this->client = new Client(SWOOLE_UNIX_STREAM);
        $this->client->set(
            [
                'open_length_check'     => true,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
                'package_max_length'    => $maxSize
            ]
        );
        $this->client->connect($unixSock, null, 3);
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->client->isConnected()) {
            $this->client->close();
        }
    }

    function close()
    {
        if ($this->client->isConnected()) {
            $this->client->close();
        }
    }

    function send(string $rawData)
    {
        if ($this->client->isConnected()) {
            return $this->client->send($rawData);
        } else {
            return false;
        }
    }

    function recv(float $timeout = 0.1)
    {
        if ($this->client->isConnected()) {
            $ret = $this->client->recv($timeout);
            if (!empty($ret)) {
                return $ret;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
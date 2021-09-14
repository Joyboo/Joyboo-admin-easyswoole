<?php


namespace EasySwoole\Socket\Tools;
use Swoole\Coroutine\Client as SwooleClient;

class Client
{
    private $client = null;

    function __construct(string $unixSock,?int $port = null)
    {
        if($port > 0){
            $this->client = new SwooleClient(SWOOLE_SOCK_TCP);
        }else{
            $this->client = new SwooleClient(SWOOLE_UNIX_STREAM);
        }

        $this->client->set(
            [
                'open_length_check' => true,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
                'package_max_length'    => 1024*1024
            ]
        );
        $this->client->connect($unixSock, $port, 3);
    }

    function client():SwooleClient
    {
        return $this->client;
    }

    function close()
    {
        if($this->client->isConnected()){
            $this->client->close();
        }
    }

    function __destruct()
    {
        $this->close();
        $this->client = null;
    }

    function send(string $rawData)
    {
        if($this->client->isConnected()){
            return $this->client->send(Protocol::pack($rawData));
        }else{
            return false;
        }
    }

    function recv(float $timeout = 0.1)
    {
        if($this->client->isConnected()){
            $ret = $this->client->recv($timeout);
            if(!empty($ret)){
                return Protocol::unpack($ret);
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
}
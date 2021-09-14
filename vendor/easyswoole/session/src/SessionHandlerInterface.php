<?php


namespace EasySwoole\Session;


interface SessionHandlerInterface
{
    function open(string $sessionId,?float $timeout = null):bool;
    function read(string $sessionId,?float $timeout = null):?array;
    function write(string $sessionId,array $data,?float $timeout = null):bool;
    function close(string $sessionId,?float $timeout = null):bool;
    function gc(int $expire,?float $timeout = null):bool;
}
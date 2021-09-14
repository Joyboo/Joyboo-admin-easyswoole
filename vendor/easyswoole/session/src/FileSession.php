<?php


namespace EasySwoole\Session;


use EasySwoole\Utility\File;

class FileSession implements SessionHandlerInterface
{
    protected $dir;

    function __construct(string $sessionDir)
    {
        if(!is_dir($sessionDir)){
            if(!File::createDirectory($sessionDir)){
                throw new Exception("fail to create session dir {$sessionDir}");
            }
        }
        $this->dir = $sessionDir;
    }

    function open(string $sessionId, ?float $timeout = null): bool
    {
        $file = "{$this->dir}/{$sessionId}";
        if(!file_exists($file)){
            if(file_put_contents($file,'') === false){
                return false;
            }
        }
        return true;
    }

    function read(string $sessionId, ?float $timeout = null): ?array
    {
        $file = "{$this->dir}/{$sessionId}";
        $data = unserialize(file_get_contents($file));
        if(is_array($data)){
            return $data;
        }else{
            return null;
        }
    }

    function write(string $sessionId, array $data, ?float $timeout = null): bool
    {
        $file = "{$this->dir}/{$sessionId}";
        $data = serialize($data);
        return (boolean)file_put_contents($file,$data);
    }

    function close(string $sessionId, ?float $timeout = null): bool
    {
        return true;
    }

    function gc(int $expire, ?float $timeout = null): bool
    {
        $list = File::scanDirectory($this->dir);

        foreach ($list['files'] as $file){
            $time = fileatime($file);
            if($expire >$time){
                unlink($file);
            }
        }

        return  true;
    }
}
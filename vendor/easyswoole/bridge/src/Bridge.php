<?php


namespace EasySwoole\Bridge;


use EasySwoole\Component\Process\Socket\UnixProcessConfig;
use EasySwoole\Socket\Tools\Client;
use Swoole\Server;

class Bridge
{
    private $socketFile;
    private $container;
    private $onStart;
    private $onException;

    function __construct(Container $container = null)
    {
        if(!$container){
            $container = new Container();
        }
        $this->container = $container;
    }

    function setOnStart(callable $call): Bridge
    {
        $this->onStart = $call;
        return $this;
    }

    function setOnException(callable $call): Bridge
    {
        $this->onException = $call;
        return $this;
    }

    function getCommandContainer():Container
    {
        return $this->container;
    }

    function attachServer(Server $server,string $serverName = 'EasySwoole')
    {
        $config = new UnixProcessConfig();
        $config->setSocketFile($this->socketFile);
        $config->setProcessName("{$serverName}.Bridge");
        $config->setProcessGroup("{$serverName}.Bridge");
        $config->setArg([
            'onStart'=>$this->onStart,
            'container'=>$this->container,
            'onException'=>$this->onException
        ]);
        $p = new BridgeProcess($config);
        $server->addProcess($p->getProcess());
    }

    function send(Package $package,float $timeout = 3.0): Package
    {
        $client = new Client($this->getSocketFile());
        $client->send(serialize($package));
        $ret = $client->recv($timeout);
        $client->close();
        $package = unserialize($ret);
        if(!$package instanceof Package){
            $package = new Package();
            $package->setMsg('connect to server fail');
            $package->setStatus(Package::STATUS_UNIX_CONNECT_ERROR);
        }
        return $package;
    }

    function call(string $command,$arg = null,float $timeout = 3.0):Package
    {
        $package = new Package();
        $package->setCommand($command);
        $package->setArgs($arg);
        return $this->send($package,$timeout);
    }

    /**
     * @return mixed
     */
    public function getSocketFile()
    {
        if(empty($this->socketFile)){
            $this->socketFile = sys_get_temp_dir().'/bridge.sock';
        }
        return $this->socketFile;
    }

    /**
     * @param mixed $socketFile
     */
    public function setSocketFile($socketFile): Bridge
    {
        $this->socketFile = $socketFile;
        return $this;
    }
}

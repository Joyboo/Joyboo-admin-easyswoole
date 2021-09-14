<?php


namespace EasySwoole\Command;


use EasySwoole\Command\AbstractInterface\CallerInterface;

class Caller implements CallerInterface
{
    private $script;
    private $command;
    private $params;

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setCommand(string $command)
    {
        $this->command = $command;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setScript(string $script)
    {
        $this->script = $script;
    }

    public function getScript(): string
    {
        return $this->script;
    }
}
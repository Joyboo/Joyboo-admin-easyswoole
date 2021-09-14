<?php


namespace EasySwoole\Bridge;


class Container
{
    private $container = [];

    public function set(CommandInterface $command,$cover = false)
    {
        if(!isset($this->container[strtolower($command->commandName())]) || $cover){
            $this->container[strtolower($command->commandName())] = $command;
        }
    }

    function get($key): ?CommandInterface
    {
        $key = strtolower($key);
        if (isset($this->container[$key])) {
            return $this->container[$key];
        } else {
            return null;
        }
    }

    function all():array
    {
        return $this->container;
    }
}
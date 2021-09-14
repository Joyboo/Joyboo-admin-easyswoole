<?php


namespace EasySwoole\Command\AbstractInterface;


interface CallerInterface
{
    public function getCommand(): string;

    public function setCommand(string $command);

    public function setParams($params);

    public function getParams();

    public function setScript(string $script);

    public function getScript(): string;
}
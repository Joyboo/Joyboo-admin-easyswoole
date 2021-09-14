<?php


namespace EasySwoole\Command\AbstractInterface;


interface CommandInterface
{
    public function commandName(): string;

    public function exec(): ?string;

    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface;

    public function desc(): string;
}
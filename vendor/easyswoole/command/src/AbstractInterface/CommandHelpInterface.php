<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\Command\AbstractInterface;


interface CommandHelpInterface
{
    public function addAction(string $actionName, string $desc);

    public function addActionOpt(string $actionOptName, string $desc);
}
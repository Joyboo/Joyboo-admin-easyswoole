<?php


namespace EasySwoole\Command\AbstractInterface;


interface ResultInterface
{
    function getResult();
    function setResult($result);
    function setMsg(?string $msg);
    function getMsg():?string ;
}
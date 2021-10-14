<?php


namespace App\Model;


class Whitelist extends Base
{
    protected $connectionName = 'sdk';

    public $sort = ['sort' => 'asc', 'id' => 'desc'];
}

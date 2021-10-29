<?php


namespace App\Model;


class Pay extends Base
{
    protected $connectionName = 'log';

    public function __construct($data = [], $tabname = '', $gameid = '')
    {
        if (!empty($gameid))
        {
            $this->tableName("order_$gameid");
        }

        parent::__construct($data);
    }
}

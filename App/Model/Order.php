<?php


namespace App\Model;


class Order extends Base
{
    protected $connectionName = 'pay';

    public function __construct($data = [], $tabname = '', $gameid = '')
    {
        if (!empty($gameid))
        {
            $this->tableName("order_$gameid");
        }

        parent::__construct($data);
    }
}

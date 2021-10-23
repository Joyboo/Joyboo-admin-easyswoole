<?php


namespace App\HttpController\Admin;

use EasySwoole\ORM\Db\MysqliClient;
use EasySwoole\ORM\DbManager;

class Expense extends Auth
{
    protected function _search()
    {
        $filter = $this->filter();

        $where = ['ymd' => [[$filter['beginday'], $filter['endday']], 'between']];

        foreach(['gameid', 'pkgbnd'] as $col)
        {
            if (isset($this->get[$col]))
            {
                $where[$col] = [explode(',', $this->get[$col]), 'in'];
            }
            elseif (!$this->isSuper())
            {
                $where[$col] = [$this->operinfo['extension'][$col], 'in'];
            }
        }
        return $where;
    }
}

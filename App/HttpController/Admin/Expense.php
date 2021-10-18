<?php


namespace App\HttpController\Admin;

use EasySwoole\ORM\Db\MysqliClient;
use EasySwoole\ORM\DbManager;

class Expense extends Auth
{
    protected function _initialize()
    {
        parent::_initialize();
        // 固定使用东八区
        $this->setPhpTimeZone('Asia/Shanghai');
    }

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

    public function index()
    {
        $this->invoke(false, '+8:00', function () { parent::index(); });
    }

    public function change()
    {
        $this->invoke(true, '+8:00', function () { parent::change(); });
    }
}

<?php


namespace App\HttpController\Admin;

use \App\Model\AdminLog as AdminLogModel;

/**
 * Class AdminLog
 * @property AdminLogModel $Model
 * @package App\HttpController\Admin
 */
class AdminLog extends Auth
{
    protected function _search()
    {
        $filter = $this->filter();

        $where = ['instime' => [[$filter['begintime'], $filter['endtime']], 'between']];
        if (isset($this->get['uid']))
        {
            $uid = $this->get['uid'];
            $this->Model->where("(uid=? OR name like ? )", [$uid, "%{$uid}%"]);
        }
        return $where;
    }

    protected function _afterIndex($items)
    {
        foreach ($items as &$value)
        {
            $value->relation = $value->relation ?? [];
        }

        return $items;
    }
}

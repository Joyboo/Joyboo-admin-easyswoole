<?php


namespace App\HttpController\Admin;

use \App\Model\LogLogin as AdminLogModel;

/**
 * 登录日志
 * Class LogLogin
 * @property AdminLogModel $Model
 * @package App\HttpController\Admin
 */
class LogLogin extends Auth
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

    protected function _afterIndex($items, $total)
    {
        foreach ($items as &$value)
        {
            $value->relation = $value->relation ?? [];
        }

        return parent::_afterIndex($items, $total);
    }
}

<?php


namespace App\HttpController\Admin;


class Log extends Auth
{
    protected function _search()
    {
        $filter = $this->filter();

        $where = ['instime' => [[$filter['begintime'], $filter['endtime']], 'between']];
        if (isset($this->get['admid']))
        {
            $where['admid'] = $this->get['admid'];
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

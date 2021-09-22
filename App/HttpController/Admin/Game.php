<?php


namespace App\HttpController\Admin;

class Game extends Auth
{
    protected function _search()
    {
        $where = [];
        if (isset($this->get['status']) && $this->get['status'] !== '')
        {
            $where['status'] = $this->get['status'];
        }
        if (!empty($this->get['name']))
        {
            $where['name'] = ["%{$this->get['name']}%", 'like'];
        }
        return $where;
    }
}

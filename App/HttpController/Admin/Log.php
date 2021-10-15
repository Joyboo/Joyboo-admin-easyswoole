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

        $content = $this->get['content'] ?? '';
        $type = $this->get['type'] ?? '';
        $type = strtoupper($type);
        if ($type && !$content)
        {
            $where['content'] = ["$type%", 'like'];
        }
        elseif ($content && !$type)
        {
            $where['content'] = ["%{$content}%", 'like'];
        }

        // 为保持下方多个like放在SQL后面
        $where && $this->Model->where($where);

        if ($content && $type) {
            $this->Model->where("(content like ? and content like ?)", ["$type%", "%$content%"]);
        }

        return false;
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

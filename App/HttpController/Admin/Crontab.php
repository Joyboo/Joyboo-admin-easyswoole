<?php


namespace App\HttpController\Admin;


class Crontab extends Auth
{
    protected function _search()
    {
        $where = [];
        foreach (['status', 'sys', 'server'] as $col)
        {
            if (isset($this->get[$col]) && $this->get[$col] !== '')
            {
                $where[$col] = $this->get[$col];
            }
        }
        foreach (['name', 'method'] as $val)
        {
            if (!empty($this->get[$val]))
            {
                $where[$val] = ["%{$this->get[$val]}%", 'like'];
            }
        }

        return $where;
    }

    protected function _afterEditGet($data)
    {
        $tmp = [];
        if ($json = $data['args'])
        {
            foreach ($json as $key => $value)
            {
                $tmp[] = [
                    'key' => $key,
                    'value' => $value
                ];
            }
        }
        $data['args'] = $tmp;

        return $data;
    }
}

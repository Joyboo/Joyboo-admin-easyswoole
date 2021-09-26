<?php


namespace App\HttpController\Admin;


use App\Common\Http\Code;

class Package extends Auth
{
    public function gkey()
    {
        $rand = [
            'logkey' => mt_rand(50, 60),
            'paykey' => mt_rand(70, 80)
        ];
        if (!isset($this->get['column']) || !isset($rand[$this->get['column']]))
        {
            return $this->error(Code::ERROR);
        }

        $sign = uniqid($rand[$this->get['column']]);

        $this->success($sign);
    }

    protected function _afterEditGet($data)
    {
        // 匹配前缀
        $prefix = 'extension.adjust.event';

        $adjust = [];
        foreach ($data as $key => $value)
        {
            $index = strpos($key, $prefix);
            if ($index !== false)
            {
                // 起始位置
                $start = $index + strlen($prefix) + 1;
                $adjust[] = [
                    'key' => substr($key, $start),
                    'value' => $value
                ];
                unset($data[$key]);
            }
        }
        $data[$prefix] = $adjust;

        $result = [
            // 常规数据
            'data' => $data,
            //
        ];
        return $data;
    }

    protected function addGet()
    {
        return $this->success();
    }

    protected function view()
    {

    }

    public function saveAdjustEvent()
    {
        var_dump($this->post['adjust']);
    }
}

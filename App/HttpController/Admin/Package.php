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

    public function saveAdjustEvent()
    {
        var_dump($this->post['adjust']);
    }
}

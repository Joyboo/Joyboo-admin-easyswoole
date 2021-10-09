<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;

/**
 * Class Game
 * @property \App\Model\Game $Model
 * @package App\HttpController\Admin
 */
class Game extends Auth
{
    protected $_uckAction = 'gkey';

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

    public function gkey()
    {
        $rand = [
            'logkey' => mt_rand(10, 20),
            'paykey' => mt_rand(30, 40)
        ];
        if (!isset($this->get['column']) || !isset($rand[$this->get['column']]))
        {
            return $this->error(Code::ERROR);
        }

        $sign = uniqid($rand[$this->get['column']]);

        $this->success($sign);
    }
}

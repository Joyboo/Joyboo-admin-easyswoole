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
        if (!isset($this->get['column']))
        {
            return $this->error(Code::ERROR);
        }
        $action = 'get' . ucfirst($this->get['column']);
        $key = $this->Model->$action();
        $this->success($key);
    }
}

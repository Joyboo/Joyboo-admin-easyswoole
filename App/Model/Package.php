<?php


namespace App\Model;


class Package extends Base
{
    public $sort = ['sort', 'asc'];

    public function getPackageAll($where = [])
    {
        if ($where) {
            $this->where($where);
        }
        return $this->order(...$this->sort)->indexBy('id');
    }
}

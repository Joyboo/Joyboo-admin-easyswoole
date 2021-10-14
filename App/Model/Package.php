<?php


namespace App\Model;


class Package extends Base
{
    public $sort = ['sort' => 'asc', 'id' => 'desc'];

    public function getPackageAll($where = [])
    {
        if ($where) {
            $this->where($where);
        }
        return $this->setOrder()->indexBy('id');
    }
}

<?php


namespace App\HttpController\Admin;

/**
 * Class Role
 * @property \App\Model\Role $Model
 * @package App\HttpController\Admin
 */
class Role extends Auth
{
    protected function _search()
    {
        $where = [];
        if (!empty($this->get['name']))
        {
            $where['name'] = ["%{$this->get['name']}%", 'like'];
        }
        if (isset($this->get['status']) && $this->get['status'] !== '')
        {
            $where['status'] = $this->get['status'];
        }
        return $where;
    }

    protected function _afterIndex($items, $total)
    {
        // 处理超级管理员菜单权限
        /** @var Menu $Menu */
        $Menu = model('Menu');
        $allMenu = $Menu->column('id');

        $super = config('SUPER_ROLE');
        foreach ($items as $key => &$val)
        {
            if ($val instanceof \EasySwoole\ORM\AbstractModel)
            {
                $val = $val->toArray();
            }
            if ($super && in_array($val['id'], $super))
            {
                $val['menu'] = $allMenu;
            } else {
                if (is_string($val['menu']))
                {
                    $val['menu'] = explode(',', $val['menu']);
                }
                $val['menu'] = array_map(function ($val) { return intval($val); }, $val['menu'] );
            }
        }
        return parent::_afterIndex($items, $total);
    }
}

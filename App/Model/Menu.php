<?php


namespace App\Model;

class Menu extends Base
{
    protected function setRedirectAttr($data, $alldata)
    {
        return $data ? '/' . ltrim($data, '/') : '';
    }

    /*protected function setComponentAttr($data, $alldata)
    {
        return ltrim($data, '/');
    }*/

    protected function setNameAttr($data, $alldata)
    {
        return ucfirst(ltrim($data, '/'));
    }

    public function menuList($ids = '')
    {
        // todo 加where 权限
        $data = $this->where([
            'status' => 1,
            'type' => [[0, 1], 'in']
        ])->order('sort', 'asc')->indexBy('id');

        $Tree = new \App\Common\Classes\Tree($data);
        $treeData = $Tree->getTree();
        $router = $this->makeRouter($treeData);
        return $router;
    }

    /**
     * 将数据转化为客户端Router结构
     */
    protected function makeRouter($data)
    {
        $result = [];
        foreach ($data as $value)
        {
            $router = [];
            foreach (['path', 'component', 'name', 'redirect', ] as $col)
            {
                $router[$col] = $value[$col] ?? '';
            }

            // meta,强类型,对应types/vue-router.d.ts
            $meta = [
                'orderNo' => intval($value['sort']),
                'title' => $value['title'],
                'ignoreAuth' => $value['ignore_auth'] == 1,
                'ignoreKeepAlive' => $value['keepalive'] != 1,
                'affix' => $value['affix'] == 1,
                'icon' => $value['icon'],
                'hideMenu' => $value['isshow'] != 1,
                'hideBreadcrumb' => $value['breadcrumb'] != 1
            ];
            // path以http开头，则认为外部链接, isext=1为外链，=0为frameSrc
            if (substr($value['path'], 0, 4) === 'http' && $value['isext'] != 1)
            {
                $meta['frameSrc'] = $value['path'];
            }
            $router['meta'] = $meta;

            if (!empty($value['children']))
            {
                $router['children'] = $this->makeRouter($value['children']);
            }
            $result[] = $router;
        }
        return $result;
    }

    /**
     * 角色组权限码
     * @param int $rid
     * @return array
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function permCode($rid):array
    {
        $where = ['permission' => ['', '<>']];
        $super = config('SUPER_ROLE');

        if (!in_array($rid, $super))
        {
            /** @var Role $Role */
            $Role = model('Role');
            $menuIds = $Role->where('id', $rid)->val('menu');
            if (empty($menuIds))
            {
                return [];
            }

            $where['id'] = [explode(',', $menuIds), 'in'];
        }
        $permission = $this->where($where)->column('permission');
        return is_array($permission) ? $permission : [];
    }
}

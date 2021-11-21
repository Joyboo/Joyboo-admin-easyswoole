<?php


namespace App\Common\Classes;

use App\Model\Menu;

class Tree
{
    /**
     * 原始数组
     * @var array
     */
    protected $menu = [];

    /**
     * 表示下级菜单的key
     * @var string
     */
    protected $childName = 'children';

    /**
     * 返回的tree限制在固定的id范围内, null 不限制
     * @var array|null
     */
    protected $ids = null;

    public function __construct($ids = null, $child = '')
    {
        if (!is_null($ids)) {
            $this->ids = is_string($ids) ? explode(',', $ids) : $ids;
        }
        $child && $this->childName = $child;
    }

    public function originData($where = [])
    {
        /** @var Menu $Menu */
        $Menu = model('Menu');
        if ($where) {
            $Menu->where($where);
        }
        $this->menu = $Menu->setOrder()->all();
        return $this;
    }

    /**
     * 获取树形数据
     * @return array
     */
    public function getTree($pid = 0, $isRouter = false): array
    {
        $tree = $this->buildMenuTree($pid);
        return $isRouter ? $this->toRouter($tree) : $tree;
    }

    public function getAll()
    {
        $arr = [];
        foreach ($this->menu as $value)
        {
            $arr[] = $value['pid'];
        }
        $min = min($arr);
        return $this->buildMenuTree($min);
    }

    /**
     * 多级菜单树
     * @param int $pid
     * @return array
     */
    protected function buildMenuTree($pid)
    {
        $result = [];
        foreach ($this->menu as $key => $value)
        {
            if ($value instanceof \EasySwoole\ORM\AbstractModel)
            {
                $value = $value->toArray();
            }
            if ($value['pid'] === $pid)
            {
//                unset($this->menu[$key]);
                // 继续找儿子
                if ($children = $this->buildMenuTree($value['id']))
                {
                    $value[$this->childName] = $children;
                }

                // 儿子在id列表爸爸不在，把爸爸也算上, 适用于 treeSelect 当子节点未选满时不会返回父节点的场景
                if (is_null($this->ids) || (is_array($this->ids) && in_array($value['id'], $this->ids) || $children))
                {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * 转化为客户端Router结构
     */
    public function toRouter($data)
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
                // 当为内嵌时，path已经不需要了，但优先级比frameSrc高，需要覆盖掉path为非url
                $router['path'] = $router['name'] ?? '';
            }
            $router['meta'] = $meta;

            if (!empty($value['children']))
            {
                $router['children'] = $this->toRouter($value['children']);
            }
            $result[] = $router;
        }
        return $result;
    }

    /**
     * 获取某一个子菜单的完整path(拼接父级)
     * @param $id
     * @return string
     */
    public function getHomePath($id)
    {
        $path = $this->buildPath($id);
        // 倒转，因为是从儿子找到爸爸的
        $path = array_reverse($path);
        return '/' . implode('/', $path);
    }

    protected function buildPath($id = 0)
    {
        $result = [];
        foreach ($this->menu as $key => $value)
        {
            if ($value instanceof \EasySwoole\ORM\AbstractModel)
            {
                $value = $value->toArray();
            }

            if ($value['id'] == $id)
            {
                $result[] = trim($value['path'], '/');
                if (!empty($value['pid']))
                {
                    // 继续找爸爸
                    $result[] = $this->buildPath($value['pid']);
                }
            }
        }
        return count($result) > 1 ? $result : current($result);
    }
}

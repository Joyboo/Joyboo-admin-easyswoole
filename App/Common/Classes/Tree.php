<?php


namespace App\Common\Classes;


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

    public function __construct(array $menu = [], $child = '')
    {
        $this->menu = $menu;
        $child && $this->childName = $child;
    }

    /**
     * 获取树形数据
     * @return array
     */
    public function getTree($pid = 0): array
    {
        return $this->buildMenuTree($pid);
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
     * 生产多级菜单树
     * @param int $pid
     * @return array
     */
    protected function buildMenuTree($pid)
    {
        $result = [];
        foreach ($this->menu as $key => $value)
        {
            if ($value['pid'] === $pid)
            {
                unset($this->menu[$key]);
                // 继续找儿子
                if ($children = $this->buildMenuTree($value['id']))
                {
                    $value[$this->childName] = $children;
                }

                $result[] = $value;
            }
        }

        return $result;
    }
}

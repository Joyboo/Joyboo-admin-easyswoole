<?php


namespace HttpController\Admin;

use PHPUnit\Framework\TestCase;

class Menu extends TestCase
{
    /** @var \App\Model\Menu  */
    protected $Model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->Model = model('Menu');
    }

    /**
     *  php easyswoole phpunit Tests/HttpController/Admin/Menu.php
     * 生成菜单树
     */
    public function testBuildMenu()
    {

        $result = $this->Model->menuList();
        $this->assertIsArray($result);
    }

    public function testTree()
    {

        $all = [
            [
                'id' => 1,
                'pid' => 0,
                'title' => '系统管理',
            ],
            [
                'id' => 2,
                'pid' => 0,
                'title' => '数据统计',
            ],
            [
                'id' => 3,
                'pid' => 1,
                'title' => '账号管理',
            ],
            [
                'id' => 4,
                'pid' => 1,
                'title' => '角色管理',
            ],
            [
                'id' => 5,
                'pid' => 1,
                'title' => '菜单管理',
            ],
            [
                'id' => 6,
                'pid' => 5,
                'title' => '添加菜单',
            ],
            [
                'id' => 7,
                'pid' => 5,
                'title' => '删除菜单',
            ]
        ];
        $Tree = new \App\Common\Classes\Tree($all);
        $data = $Tree->getAll();
        var_dump($data);
        $this->assertIsArray($data);
    }
}

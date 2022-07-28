<?php


namespace HttpController\Admin;

use PHPUnit\Framework\TestCase;
use WonderGame\EsUtility\Common\Classes\Tree;

class Menu extends TestCase
{
    /** @var \App\Model\Admin\Menu  */
    protected $Model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->Model = model_admin('Menu');
    }

    /**
     *  php easyswoole phpunit Tests/HttpController/Admin/Menu.php
     * 生成菜单树
     */
    public function testBuildMenu()
    {

        $result = $this->Model->getTree();
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
        $Tree = new Tree(['data' => $all]);
        $data = $Tree->getAll();
        print_r($data);
        $this->assertIsArray($data);
    }
}

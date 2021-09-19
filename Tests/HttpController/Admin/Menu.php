<?php


namespace HttpController\Admin;

use PHPUnit\Framework\TestCase;

class Menu extends TestCase
{
    /**
     *  php easyswoole phpunit Tests/HttpController/Admin/Menu.php
     * 生成菜单树
     */
    public function testBuildMenu()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $result = $model->menuList();
        $this->assertIsArray($result);
    }
}

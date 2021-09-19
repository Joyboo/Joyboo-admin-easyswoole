<?php

use EasySwoole\DatabaseMigrate\MigrateManager;

/**
 * filling data
 *
 * Class Menu
 */
class Menu
{
    /**
     * 创建： php easyswoole migrate seed --create=Admin
     * 执行填充：php easyswoole migrate seed
     * seeder run
     * @return void
     * @throws Throwable
     * @throws \EasySwoole\Mysqli\Exception\Exception
     */
    public function run()
    {
        $father = [
            'pid' => 0,
            'type' => 0,
            'name' => 'System',
            'title' => '系统管理',
            'sort' => 1,
            'icon' => 'ion:settings-outline',
            'path' => '/system',
            'component' => 'LAYOUT',
            'redirect' => '/system/account',
        ];

        $client = MigrateManager::getInstance()->getClient();
        $client->queryBuilder()->insert("menu", $father);
        $client->execBuilder();

        // 获取自增id为子菜单父级id
        $lastId = $client->mysqlClient()->insert_id;
        $son = [
            [
                'pid' => $lastId,
                'type' => 1,
                'name' => 'AccountManagement',
                'title' => '账号管理',
                'sort' => 9,
                'path' => 'account',
                'component' => '/admin/system/account/index',
            ],
            [
                'pid' => $lastId,
                'type' => 1,
                'name' => 'RoleManagement',
                'title' => '角色管理',
                'sort' => 9,
                'path' => 'role',
                'component' => '/admin/system/role/index',
            ],
            [
                'pid' => $lastId,
                'type' => 1,
                'name' => 'MenuManagement',
                'title' => '菜单管理',
                'sort' => 9,
                'path' => 'menu',
                'component' => '/admin/system/menu/index',
            ]
        ];
        $client->queryBuilder()->insert("menu", $son);
        $client->execBuilder();
    }
}

<?php

use EasySwoole\DatabaseMigrate\MigrateManager;

/**
 * filling data
 *
 * Class Role
 */
class Role
{
    /**
     * php easyswoole migrate seed Role
     * seeder run
     * @return void
     * @throws Throwable
     * @throws \EasySwoole\Mysqli\Exception\Exception
     */
    public function run()
    {
        $insert = [
            'name' => '超级管理员',
            'value' => 'Super',
            'sort' => 1,
            'remark' => '拥有最高权限',
            'menu' => '*',
            'instime' => time()
        ];
        $client = MigrateManager::getInstance()->getClient();
        $client->queryBuilder()->insert("role", $insert);
        $client->execBuilder();

        $lastId = $client->mysqlClient()->insert_id;
        $insert = [
            'username' => 'admin',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'realname' => 'Joyboo',
            'rid' => $lastId,
            'sort' => 1,
            'extension' => json_encode([]),
            'instime' => time()
        ];

        $client = MigrateManager::getInstance()->getClient();
        $client->queryBuilder()->insert("admin", $insert);
        $client->execBuilder();
    }
}

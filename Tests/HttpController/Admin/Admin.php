<?php


namespace HttpController\Admin;

use PHPUnit\Framework\TestCase;

class Admin extends TestCase
{
    /**
     * php easyswoole phpunit Tests/HttpController/Admin/Admin.php
     * 获取用户信息
     */
    public function testGetUserInfo()
    {
        // todo
    }

    public function testMakeAdminPassword()
    {
        $pwd = password_hash('123456', PASSWORD_DEFAULT);
        echo "\n $pwd \n";
        $this->assertIsString($pwd);

//        $hash = '$2y$10$667SIeM/EVCYckHQ5eIlFOPv4zoR8tv6fpruHbns5tRJOplJJrk9C';
//        $this->assertEquals($pwd, $hash);
    }
}

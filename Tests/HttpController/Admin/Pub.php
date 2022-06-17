<?php


namespace HttpController\Admin;

use App\Common\Exception\HttpParamException;
use PHPUnit\Framework\TestCase;

class Pub extends TestCase
{
    /**
     * php easyswoole phpunit Tests/HttpController/Admin/Pub.php
     */
    public function testUserLogin()
    {
        /** @var \App\Model\Admin\Admin $model */
        $model = model_admin('Admin');

        $SwRequest = new \Swoole\Http\Request();
        $SwRequest->header['x-real-ip'] = ['127.0.0.1'];
        $request = new \EasySwoole\Http\Request($SwRequest);

        $result = [];
        try {
            $result = $model->login(['username' => 'admin', 'password' => '123456'], $request);
        }
        catch (HttpParamException $e)
        {
        }

        $this->assertArrayHasKey('token', $result);
    }
}

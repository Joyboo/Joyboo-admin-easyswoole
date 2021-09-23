<?php


namespace HttpController\Admin;

use PHPUnit\Framework\TestCase;
use App\Common\Classes\Extension;

class Game extends TestCase
{
    /**
     * 数据库原数据
     * @var array
     */
    protected $origin = [
        'id' => 1,
        'name' => 'origin name',
        'extension' => [
            'type' => 1,
            'logkey' => 'origin logkey',
            'paykey' => 'origin paykey',
            'mtn' => [
                'switch' => 1,
                'begintime' => '2021-09-23 16:18:00',
                'endtime' => '2021-09-23 16:28:00',
                'notice' => 'Hello Notice'
            ],
            'facebook' => [
                'fansurl' => ''
            ],
            'google' => [
                'privacy' => ''
            ]
        ]
    ];

    /**
     * 客户端提交的数据
     * @var array
     */
    protected $post = [
        'id' => 1,
        'name' => 'new name',
        'extension.type' => 1,
        'extension.logkey' => 'new Logkey',
        'extension.mtn.switch' => 0,
        'extension.facebook.fansurl' => 'https://github.com',
        'extension.google.privacy' => 'https://github.com',
        'extension.not.a1' => 0,
        'extension.not.a2' => '',
    ];

    /**
     * php easyswoole phpunit Tests/HttpController/Admin/Game.php
     * 将客户端提交的post合并到origin,允许新增，少了的字段保持原值
     */
    public function testGetSave()
    {
        $Extension = new Extension();
        $Extension->setPost($this->post);
        $Extension->setOrigin($this->origin);
        $save = $Extension->getSave();

        echo "\n\n ======== save ========== \n\n";
        print_r($save);
        $this->assertIsArray($save);
    }

    /**
     * 将数据库的结构拍平发送给客户端，即origin格式转化为post格式
     */
    public function testGetTemplate()
    {
        $Extension = new Extension();
        $Extension->setPost($this->post);
        $Extension->setOrigin($this->origin);
        $template = $Extension->getTemplate();

        echo "\n\n ======== template ========== \n\n";
        print_r($template);
        $this->assertIsArray($template);
    }
}

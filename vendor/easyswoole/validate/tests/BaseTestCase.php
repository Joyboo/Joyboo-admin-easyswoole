<?php

namespace EasySwoole\Validate\tests;

use EasySwoole\Validate\Validate;
use PHPUnit\Framework\TestCase;

/**
 * 基础测试环境
 * Class BaseTestCase
 *
 * @internal
 */
class BaseTestCase extends TestCase
{
    /** @var Validate */
    protected $validate;

    // 建立测试基境 引入必要文件
    public function setUp(): void
    {
        /*require_once dirname(__FILE__) . '/../src/Rule.php';
        require_once dirname(__FILE__) . '/../src/Error.php';
        require_once dirname(__FILE__) . '/../src/Validate.php';*/
        $this->freeValidate();
        parent::setUp();
    }

    // 验证器是否已经实例化成功
    public function testValidateClass()
    {
        $this->assertInstanceOf(Validate::class, $this->validate, 'validate is not instance of Validate class');
    }

    // 释放并初始化验证器
    public function freeValidate()
    {
        $this->validate = new Validate();
    }
}

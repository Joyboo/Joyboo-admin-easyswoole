<?php
/**
 * @CreateTime:   2019/12/7 下午10:59
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  兼容php7.4约束类型单测
 */
namespace EasySwoole\Spl\Test\Bean;

use EasySwoole\Spl\SplBean;

class TestAllProperty extends SplBean{

    // static
    public static $staticA;
    protected static $staticB;
    private static $staticC;

    // static赋初值
    public static $staticInitA='';
    protected static $staticInitB='';
    private static $staticInitC='';

    // 普通
    public $a;
    protected $b;
    private $c;

    // 普通赋初值
    public $aInit='';
    protected $bInit='';
    private $cInit='';

}

class TestAllProperty74 extends SplBean{

    // static
    public static $staticA;
    protected static $staticB;
    private static $staticC;

    // static赋初值
    public static $staticInitA='';
    protected static $staticInitB='';
    private static $staticInitC='';

    // 普通
    public $a;
    protected $b;
    private $c;

    // 普通赋初值
    public $aInit='';
    protected $bInit='';
    private $cInit='';

    // 约束类型
    public string $typeA;
    protected int $typeB;
    private bool $typeC;

    // 约束类型赋初值
    public string $typeInitA='';
    protected int $typeInitB=1;
    private bool $typeInitC=true;

}

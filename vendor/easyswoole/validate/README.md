# validate

## 默认错误信息提示

`validate`验证器提供了默认错误信息规则,[点击查看](./src/Error.php).

```php
<?php

require_once "./vendor/autoload.php";

$data = ['name' => 'blank', 'age' => 25];   // 验证数据
$validate = new \EasySwoole\Validate\Validate();
$validate->addColumn('name')->required();   // 给字段加上验证规则
$validate->addColumn('age')->required()->max(18);
$bool = $validate->validate($data); // 验证结果
if ($bool) {
    var_dump("验证通过");
} else {
    var_dump($validate->getError()->__toString());
}
/*
 * 输出结果： string(23) "age的值不能大于18"
 */
```

## 自定义错误信息提示

```php
<?php

require_once "./vendor/autoload.php";

$data = ['name' => 'blank', 'age' => 25];   // 验证数据
$validate = new \EasySwoole\Validate\Validate();
$validate->addColumn('name')->required('名字不为空');   // 给字段加上验证规则
$validate->addColumn('age')->required('年龄不为空')->func(function ($itemData, $column, \EasySwoole\Validate\Validate $validate) {
    return ($validate->getVerifyData() instanceof SplArray) && $column === 'callback' && $itemData === 0.001;
},'只允许18岁的进入');
$bool = $validate->validate($data); // 验证结果
if ($bool) {
    var_dump("验证通过");
} else {
    var_dump($validate->getError()->__toString());
}
/*
 * 输出结果： string(23) "只允许18岁的进入"
 */
```

## 自定义验证器类

```php
<?php

require_once "./vendor/autoload.php";

class CustomValidator extends \EasySwoole\Validate\Functions\AbstractValidateFunction
{
    /**
     * 返回当前校验规则的名字
     */
    public function name(): string
    {
        return 'mobile';
    }

    /**
     * 失败在里面做异常也可以
     * @param $itemData
     * @param $arg
     * @param $column
     * @return bool
     */
    public function validate($itemData, $arg, $column, \EasySwoole\Validate\Validate $validate): bool
    {
        $regular = '/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/';
        if (!preg_match($regular, $itemData)) {
            return false;
        }

        return true;
    }
}
// 待验证数据
$data     = ['mobile' => '12312345678'];
$validate = new \EasySwoole\Validate\Validate();
// 先添加function 第一个参数为类 第二个参数 是否覆盖 当存在相同名字的验证规则 参数true会替换掉
$validate->addFunction(new CustomValidator(),false);
// 自定义错误消息示例
$validate->addColumn('mobile')->required('手机号不能为空')->callUserRule(new CustomValidator(), '手机号格式不正确');
$bool = $validate->validate($data); // 验证结果
if ($bool) {
    var_dump("验证通过");
} else {
    var_dump($validate->getError()->__toString());
}
/*
 * 输出结果：string(24) "手机号格式不正确"
 */
```

## 带*匹配规则

```php
<?php

$validate = new \EasySwoole\Validate\Validate();
// * 可以放在任意位置 且有多个
$validate->addColumn('*.a')->required()->notEmpty()->between(1,10);
$bool = $validate->validate([
            'a' => ['a' => 2],
            'b' => ['a' => 11]
        ]);
if ($bool) {
    var_dump("验证通过");
} else {
    var_dump($validate->getError()->__toString());
}
/*
 * 输出结果 *.a只能在 1 - 10 之间
 */
```

## 快速验证

提供了数组快速验证方式.

`EasySwoole\Validate\Validate::make()`:

参数:

- `$rules` 验证规则.
- `$message` 自定义错误信息.
- `$alias` 字段别名.

返回:

- `\EasySwoole\Validate\Validate::class`实例.

Eg:

```php
$rules = [
    'name' => 'required|notEmpty',
    'age' => 'required|integer|between:20,30',
    'weight' => 'required|max:50'
];
$messages = [
    'name.required' => '名字不能为空呀！',
    'age' => '年龄输入有误呀！',
    'weight.max' => '体重最大不能超过50呀！'
];
$alias = [
    'name' => '名字',
    'age' => '年龄',
    'weight' => '体重'
];
$validate = \EasySwoole\Validate\Validate::make($rules,$messages,$alias);
$bool = $validate->validate([
            'name' => '史迪仔',
            'age' => 20,
            'weight' => 70
        ]);
if ($bool) {
    var_dump("验证通过");
} else {
    var_dump($validate->getError()->__toString());
}
/*
 * 输出结果：weight的值不能大于'50'
 */
```

> 暂不支持 `inArray`,`notInArray`,`func`,`callUserRule`,`allowFile`,`allowFileType`等规则.

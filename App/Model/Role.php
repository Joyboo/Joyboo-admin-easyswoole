<?php


namespace App\Model;


class Role extends Base
{
    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = false;

    public $sort = ['sort', 'asc'];

    protected function setMenuAttr($data, $alldata)
    {
        $super = config('SUPER_ROLE');
        if (is_array($data))
        {
            $data = implode(',', $data);
        }
        // 超级管理员永远返回*
        if (isset($alldata['id']) && $super && in_array($alldata['id'], $super))
        {
            return '*';
        }
        return $data;
    }

    protected static function onBeforeDelete(Role $model)
    {
        // 超级管理员不可删除
        $data = $model->toArray();
        $super = config('SUPER_ROLE');
        $isSuper = $super && in_array($data['id'], $super);
        return !$isSuper;
    }
}

<?php


namespace App\Model;

use EasySwoole\Mysqli\QueryBuilder;

class Admin extends Base
{
    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = false;

    public $sort = ['sort' => 'asc', 'id' => 'desc'];

    protected function setPasswordAttr($password = '', $alldata = [])
    {
        if($password != '')
        {
            return password_hash($password, PASSWORD_DEFAULT);
        }
        return false;
    }

    /**
     * 关联Role分组模型
     * @return array|mixed|null
     * @throws \Throwable
     */
    public function relation()
    {
        $callback = function(QueryBuilder $query){
            $query->where('status', 1);
            return $query;
        };

        return $this->hasOne(Role::class, $callback, 'rid', 'id');
    }

    /**
     * 生成role->admin管理员树
     * @param string $parid 值
     * @param string $parkey 键
     * @return array[]
     */
    public function getGive($parid = '', $parkey = 'gameids')
    {
        // 所有管理员
        $admin = $this->setOrder()->field('id,rid,username,realname,status,extension')->all();
        // 由于数据库存在id为0的游戏，不能使用find_in_set
        $authId = [];
        if ($parid != '')
        {
            foreach ($admin as $key => $value)
            {
                $extension = $value->getAttr('extension');
                if (empty($value['status']))
                {
                    continue;
                }
                if (isset($extension[$parkey]))
                {
                    $gids = $extension[$parkey];

                    if (is_string($gids))
                    {
                        $gids = explode(',', $gids);
                    }

                    // 超级管理员给全部权限，并禁用
                    if (in_array($parid, $gids) || isSuper($value['rid']))
                    {
                        $authId[] = $value['rid'] . '-' . $value['id'];
                    }
                }
            }
        }

        // 分组
        /** @var Role $Role */
        $Role = model('Role');
        $role = $Role->field(['id', 'name'])->all();

        $result = [];
        foreach ($role as $rv)
        {
            $_admins = [];
            /** @var Admin $ad */
            foreach ($admin as $ad)
            {
                $ad = $ad->toArray();
                if (empty($ad['status']))
                {
                    continue;
                }
                if ($ad['rid'] == $rv['id'])
                {
                    $ad['key'] = $rv['id'] . '-' . $ad['id'];
                    $ad['title'] = "{$ad['realname']}-{$ad['username']}-{$ad['id']}";
                    $ad['disabled'] = isSuper($rv['id']);
                    $_admins[] = $ad;
                }
            }

            if ($_admins)
            {
                $result[] = [
                    'key' => $rv['id'],
                    'rid' => $rv['id'],
                    'title' => $rv['name'],
                    'disabled' => isSuper($rv['id']),
                    'children' => $_admins // 该组所有管理员
                ];
            }
        }

        return [
            'tree' => $result, // 树形结构，第一级为角色组，第二级为每个组管理员
            'auth' => $authId, // 有该游戏权限的管理员
        ];
    }
}

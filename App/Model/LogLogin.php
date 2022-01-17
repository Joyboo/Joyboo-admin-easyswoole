<?php


namespace App\Model;


use EasySwoole\Mysqli\QueryBuilder;

class LogLogin extends Base
{
    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = 'updtime';

    protected function getIpAttr($ip = [], $data = [])
    {
        return is_numeric($ip) ? long2ip($ip) : $ip;
    }

    protected function setIpAttr($ip, $data = [])
    {
        return is_numeric($ip) ? $ip : ip2long($ip);
    }

    /**
     * 关联
     * @return array|mixed|null
     * @throws \Throwable
     */
    public function relation()
    {
        $callback = function(QueryBuilder $query){
            $query->fields(['id', 'username', 'realname', 'avatar', 'status']);
            return $query;
        };
        return $this->hasOne(Admin::class, $callback, 'uid', 'id');
    }
}
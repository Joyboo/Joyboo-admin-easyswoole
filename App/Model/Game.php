<?php


namespace App\Model;


class Game extends Base
{
    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = false;

    public $sort = ['sort', 'asc'];

    public function getLogkey($logkey = '')
    {
        return uniqid(mt_rand(10,20));
    }

    public function getPaykey($paykey = '')
    {
        return uniqid(mt_rand(30,40));
    }

    protected function setLogurlAttr($logurl = '')
    {
        if($logurl)
        {
            $logurl = rtrim($logurl, ' ?&');
            return  $logurl . ( strpos($logurl, '?')===false ? '?' : '&' );
        }
        return '';
    }

    protected function setPayurlAttr($payurl = '')
    {
        if($payurl)
        {
            $payurl = rtrim($payurl, ' ?&');
            return  $payurl . ( strpos($payurl, '?')===false ? '?' : '&' );
        }
        return '';
    }

    public function getGameAll($where = [])
    {
        if ($where) {
            $this->where($where);
        }
        return $this->where('status', 1)->order(...$this->sort)->all('id');
    }
}

<?php

namespace App\Model;

class HttpTracker extends Base
{
    protected $tableName = 'http_tracker';

    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = false;

    public $sort = ['instime' => 'desc'];

    // starttime和endtime是小数点后有四位的时间戳字符串,转为int毫秒时间戳
    protected function _trackerTime($timeStamp)
    {
        return $timeStamp ? intval($timeStamp * 1000) : $timeStamp;
    }

    protected function getStartTimeAttr($val)
    {
        return $this->_trackerTime($val);
    }

    protected function getEndTimeAttr($val)
    {
        return $this->_trackerTime($val);
    }

    protected function getUrlAttr($val)
    {
        return urldecode($val);
    }

    protected function getRequestAttr($val)
    {
        $arr = [];
        if ($json = json_decode($val, true))
        {
            $arr = $json;
        }
        return arrayToStd($arr);
    }
    protected function getResponseAttr($val)
    {
        $json = json_decode($val, true);
        $arr = is_array($json) ? $json : [];
        return arrayToStd($arr);
    }
}

<?php


namespace App\Model;


class Crontab extends Base
{
    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = false;

    protected function setServerAttr($data)
    {
        return is_array($data) ? implode(',', $data) : $data;
    }

    protected function setSysAttr($data)
    {
        return is_array($data) ? implode(',', $data) : $data;
    }

    protected function getServerAttr($value, $alldata)
    {
        return $this->getIntArray($value);
    }

    protected function getSysAttr($value, $alldata)
    {
        return $this->getIntArray($value);
    }

    protected function getIntArray($value)
    {
        if (is_string($value))
        {
            $array = explode(',', $value);
            return array_map(function($val) {
                return intval($val);
            }, $array);
        } else {
            return $value;
        }
    }

    protected function setArgsAttr($data)
    {
        $result = [];
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                // 不要空值
                if (empty($value['key']) || empty($value['value'])) {
                    continue;
                }
                // 需要将双引号替换成单引号，否则eval解析失败
                $value['value'] = str_replace('"', '\'', $value['value']);
                $result[$value['key']] = $value['value'];
            }
        }
        return json_encode($result);
    }

    protected function getArgsAttr($data)
    {
        $json = json_decode($data, true);
        return $json ? $json : '';
    }

    public function getCrontab()
    {
        // 1-启用,2-运行一次
        return $this->where(['status' => [[1, 2], 'in']])->all();
    }
}

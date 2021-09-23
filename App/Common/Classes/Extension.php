<?php


namespace App\Common\Classes;


class Extension
{
    /**
     * 数据库原数据
     * @var array
     */
    private $origin = [];

    /**
     * 客户端提交的数据
     * @var array
     */
    private $post = [];

    /**
     * 分割标识符
     * @var string
     */
    private $split = '.';

    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * 将客户端提交的post合并到origin, 允许新增，少了的字段保持原值
     * @return array
     */
    public function getSave()
    {
        $save = $this->origin;
        foreach ($this->post as $key => $value)
        {
            $deep = $this->mergeToSave($key, $value);
            $save = array_merge_multi($save, $deep);
        }
        return $save;
    }

    /**
     * 将数据库的结构拍平发送给客户端，即origin格式转化为post格式
     */
    public function getTemplate()
    {
        if (empty($this->origin))
        {
            return [];
        }
        $template = [];
        foreach ($this->origin as $key => $value)
        {
            $this->toPostStruct($template, $key, $value);
        }

        return $template;
    }

    protected function mergeToSave($key, $value = '')
    {
        if (strpos($key, $this->split) === false)
        {
            return [$key => $value];
        }

        $result = [];
        list ($first, $last) = explode($this->split, $key, 2);

        if (strpos($last, $this->split) !== false)
        {
            $result[$first] = $this->mergeToSave($last, $value);
        }
        else
        {
            return [$first => [ $last => $value]];
        }
        return $result;
    }

    /**
     * 将数据库extension结构转换为post类型
     * 找到每一个叶子节点
     */
    protected function toPostStruct(& $sign, $key, $value)
    {
        if (is_array($value))
        {
            foreach ($value as $lk => $lv)
            {
                $fullKey = $key . $this->split . $lk;
                if (is_array($lv))
                {
                    $this->toPostStruct($sign, $fullKey, $lv);
                } else {
                    $sign[$fullKey] = $lv;
                }
            }
        } else {
            $sign[$key] = $value;
        }
    }
}

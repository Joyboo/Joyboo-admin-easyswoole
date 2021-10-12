<?php


namespace App\HttpController\Admin;


use App\Common\Exception\HttpParamException;
use App\Common\Http\Code;
use App\Common\Languages\Dictionary;

/**
 * Class Sysinfo
 * @property \App\Model\Sysinfo $Model
 * @package App\HttpController\Admin
 */
class Sysinfo extends Auth
{
    protected function _writeBefore()
    {
        $post = $this->post;
        if (empty($post['varname']) || empty($post['value']) || !isset($post['type']))
        {
            return $this->error(Code::ERROR);
        }

        if ($post['type'] == $this->Model::TYPE_ARRAY)
        {
            try {
                $value = $this->Model->toArraybyEval($post['value']);
                if (!is_array($value)) {
                    throw new \Exception();
                }
            } catch (\Exception | \Throwable $e)
            {
                throw new HttpParamException('检测到语法错误，请重新输入');
            }
        }
    }

    /*protected function _afterIndex($items)
    {
        foreach ($items as &$value)
        {
            $value = $value->toArray();

            if ($value['type'] == $this->Model::TYPE_ARRAY)
            {
                $return = $this->Model->toArraybyEval($value['value'], true);
                if ($return !== false) {
                    $value['value'] = $return;
                }
            }
        }
        return $items;
    }*/
}

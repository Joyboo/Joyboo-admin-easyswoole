<?php


namespace EasySwoole\Annotation;


class ValueParser
{
    public static function parser(?string $raw):array
    {
        $params = static::explodeSubject($raw);

        array_walk_recursive($params,function (&$value,$key){
            $value = static::eval($value);
        });

        return $params;
    }

    protected static function explodeSubject(string $raw):array
    {
        $allParams = [];
        $hasQuotation = 0;
        $hasArray = 0;
        $temp = '';
        $len =  strlen($raw);
        for($i = 0;$i < $len;$i++){
            $char = $raw[$i];
            if($char == "{"){
                $hasArray++;
            }else if($char == "}"){
                $hasArray--;
            }
            if($char == '"'){
                $hasQuotation = ~$hasQuotation;
            }
            //如果不在引号内，删除空格
            if(!$hasQuotation && $char == ' '){
                $char = '';
            }
            //不在引号或者是数组内，遇到逗号结束
            if(($hasArray == false) && ($hasQuotation === 0) && ($char == ',')){
                $allParams[] = $temp;
                $temp = '';
            }else{

                $temp .= $char;
            }
        }
        /*
         * 追加最后的参数值
         */
        if(!empty($temp)){
            $allParams[] = $temp;
        }

        $ret = [];
        $key = -1;
        foreach ($allParams as $val){
            $pos = strpos($val,'=');
            if($pos > 0){
                $key = substr($val,0,$pos);
                $val = substr($val,$pos + 1);
            }else{
                $key++;
            }
            if(substr($val,0,1) == '{' && substr($val,-1,1) == '}'){
                $val = static::explodeSubject(substr($val,1,-1));
            }
            $ret[$key] = $val;
        }
        return $ret;
    }

    protected static function eval($value)
    {
        //取出两边的双引号
        $hasQuote = 0;
        if(substr($value,0,1) == '"'){
            $value = substr($value,1);
            $hasQuote++;
        }
        if(substr($value,-1,1) == '"'){
            $value = substr($value,0,-1);
            $hasQuote++;
        }
        if(substr($value,0,5) == 'eval('  && substr($value,-1,1) == ')'){
            $value =  substr($value,5,strlen($value) - 6);
            return eval("return {$value} ;");
        }if($value == 'true'){
             $value = true;
        }else if($value == 'false'){
            $value = false;
        }else if($value == 'null'){
            $value = null;
        }else if(is_numeric($value)){
            if((abs($value) - abs(intval($value)) < 0.00001)){
                return intval($value);
            }else{
                return floatval($value);
            }
        }
        return $value;
    }
}
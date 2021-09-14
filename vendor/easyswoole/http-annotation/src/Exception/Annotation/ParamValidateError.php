<?php


namespace EasySwoole\HttpAnnotation\Exception\Annotation;


use EasySwoole\HttpAnnotation\Exception\Exception;
use EasySwoole\Validate\Validate;

class ParamValidateError extends Exception
{
    /**
     * @var Validate
     */
    private $validate;

    /**
     * @return Validate
     */
    public function getValidate(): ?Validate
    {
        return $this->validate;
    }

    /**
     * @param Validate $validate
     */
    public function setValidate(Validate $validate): void
    {
        $this->validate = $validate;
    }
}
<?php


namespace EasySwoole\HttpAnnotation\Annotation;

use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\Controller;

class ObjectAnnotation extends AnnotationBean
{
    /** @var ApiGroup|null */
    protected $apiGroup;
    /** @var ApiGroupDescription|null */
    protected $apiGroupDescription;
    /** @var Controller|null */
    protected $controller;

    protected $apiGroupAuth = [];

    protected $param = [];

    protected $__methods = [];

    protected $__properties = [];


    function getGroupAuthTag(?string $paramName = null)
    {
        if($paramName){
            if(isset($this->apiGroupAuth[$paramName])){
                return $this->apiGroupAuth[$paramName];
            }
            return null;
        }else{
            return $this->apiGroupAuth;
        }
    }

    public function getParamTag(?string $paramName = null)
    {
        if($paramName){
            if(isset($this->param[$paramName])){
                return $this->param[$paramName];
            }
            return null;
        }else{
            return $this->param;
        }
    }

    function addProperty(PropertyAnnotation $annotation)
    {
        $this->__properties[$annotation->getName()] = $annotation;
        return $this;
    }

    function getProperty(?string $name = null)
    {
        if($name){
            if(isset($this->__properties[$name])){
                return $this->__properties[$name];
            }
            return null;
        }else{
            return $this->__properties;
        }
    }

    function getMethod(?string $name = null)
    {
        if($name){
            if(isset($this->__methods[$name])){
                return $this->__methods[$name];
            }
            return null;
        }else{
            return $this->__methods;
        }
    }

    function addMethod(MethodAnnotation $method)
    {
        $this->__methods[$method->getMethodName()] = $method;
        return $this;
    }

    /**
     * @return ApiGroup|null
     */
    public function getApiGroupTag(): ?ApiGroup
    {
        return $this->apiGroup;
    }

    /**
     * @return ApiGroupDescription|null
     */
    public function getApiGroupDescriptionTag(): ?ApiGroupDescription
    {
        return $this->apiGroupDescription;
    }

    /**
     * @return Controller|null
     */
    public function getController(): ?Controller
    {
        return $this->controller;
    }
}


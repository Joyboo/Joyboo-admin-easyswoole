<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\HttpAnnotation\AnnotationTag\Context;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\Inject;

class PropertyAnnotation extends AnnotationBean
{
    protected $name;
    /** @var Di|null */
    protected $di;
    /** @var Context|null */
    protected $context;

    /**
     * @var Inject|null
     */
    protected $inject;

    function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Di|null
     */
    public function getDiTag(): ?Di
    {
        return $this->di;
    }

    public function getInjectTag():?Inject
    {
        return $this->inject;
    }


    /**
     * @return Context|null
     */
    public function getContextTag(): ?Context
    {
        return $this->context;
    }

}
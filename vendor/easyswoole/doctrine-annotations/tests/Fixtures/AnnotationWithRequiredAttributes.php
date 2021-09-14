<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAnnotation;

/**
 * @Annotation
 * @Target("ALL")
 * @Attributes({
      @Attribute("value",   required = true ,   type = "string"),
      @Attribute(
          "annot",
          required = true ,
          type = "EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAnnotation"
      ),
   })
 */
final class AnnotationWithRequiredAttributes
{
    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /** @var string */
    private $value;

    /** @var AnnotationTargetAnnotation */
    private $annot;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return AnnotationTargetAnnotation
     */
    public function getAnnot()
    {
        return $this->annot;
    }
}

<?php


namespace EasySwoole\Annotation;



use EasySwoole\DoctrineAnnotation\AnnotationReader;

class Annotation
{
    protected $parserTagList = [];
    protected $aliasMap = [];
    protected $strictMode = false;

    function __construct(array $parserTagList = [])
    {
        $this->parserTagList = $parserTagList;
    }

    function addAlias(string $alias,string $realTagName)
    {
        $this->aliasMap[$realTagName] = $alias;
        return $this;
    }

    public function strictMode(bool $is)
    {
        $this->strictMode = $is;
        return $this;
    }


    function addParserTag(AbstractAnnotationTag $annotationTag):Annotation
    {
        $name = $annotationTag->tagName();
        if(isset($this->aliasMap[$name])){
            throw new Exception("tag alias name {$name} and tag name is duplicate");
        }
        $this->parserTagList[$name] = $annotationTag;
        return $this;
    }

    function deleteParserTag(string $tagName):Annotation
    {
        unset($this->parserTagList[$tagName]);
        return $this;
    }


    function getAnnotation(\Reflector $ref):array
    {
        $ret = [];
        $reader = new AnnotationReader();
        if($ref instanceof \ReflectionMethod){
            $temp = $reader->getMethodAnnotations($ref);
        }else if($ref instanceof \ReflectionProperty){
            $temp = $reader->getPropertyAnnotations($ref);
        }else if($ref instanceof \ReflectionClass){
            $temp = $reader->getClassAnnotations($ref);
        }
        if(!empty($temp)) {
            foreach ($temp as $item){
                if($item instanceof AbstractAnnotationTag){
                    $name = $item->tagName();
                    $item->__onParser();
                    if(isset($this->parserTagList[$name])){
                        $ret[$name][] = $item;
                        if(isset($this->aliasMap[$name])){
                            $alias = clone $item;
                            $alias->aliasFrom($name);
                            $alias->__onParser();
                            $name = $this->aliasMap[$name];
                            $ret[$name][] = $alias;
                        }
                    }
                }
            }
        }
        return $ret;
    }
}
<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\Controller;

/**
 * Class RouterPath
 * @package EasySwoole\HttpAnnotation\Tests\TestController
 * @Controller(prefix="/Router")
 */
class RouterPath extends AnnotationController
{
    /**
     * @Api(name="test",path="/test")
     */
    public function test()
    {
        $this->response()->write(__CLASS__ . '::' . __FUNCTION__);
    }

    /**
     * @Api(name="test",path="/")
     */
    public function none()
    {
        $this->response()->write('none');
    }

    /**
     * @Api(name="ignorePrefix",path="/ignore",ignorePrefix=true)
     */
    public function ignore()
    {
        $this->response()->write('ignorePrefix');
    }
}

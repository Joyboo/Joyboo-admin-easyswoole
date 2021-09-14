<?php


namespace EasySwoole\HttpAnnotation;


use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class AnnotationREST extends AnnotationController
{
    /*
       *支持方法
       'GET',      // 从服务器取出资源（一项或多项）
       'POST',     // 在服务器新建一个资源
       'PUT',      // 在服务器更新资源（客户端提供改变后的完整资源）
       'PATCH',    // 在服务器更新资源（客户端提供改变的属性）
       'DELETE',   // 从服务器删除资源
       'HEAD',     // 获取 head 元数据
       'OPTIONS',  // 获取信息，关于资源的哪些属性是客户端可以改变的
     */
    function __hook(?string $actionName, Request $request, Response $response):?string
    {
        $actionName = $request->getMethod().ucfirst($actionName);
        return parent::__hook($actionName, $request, $response);
    }
}

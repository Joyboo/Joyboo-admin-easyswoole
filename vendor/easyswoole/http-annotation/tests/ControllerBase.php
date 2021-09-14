<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

trait ControllerBase
{
    protected function fakeRequest(string $requestPath = '/',array $query = null,array $post = []):Request
    {
        if($query === null){
            $query = [
                "groupParamA"=>"groupParamA",
                'groupParamB'=>"groupParamB"
            ];
        }else if(!empty($query)){
            $query = $query + [
                    "groupParamA"=>"groupParamA",
                    'groupParamB'=>"groupParamB"
                ];
        }
        $request = new Request();
        $request->getUri()->withPath($requestPath);
        //全局的参数
        $request->withQueryParams($query);
        if(!empty($post)){
            $request->withMethod('POST')->withParsedBody($post);
        }else{
            $request->withMethod('GET');
        }
        return $request;
    }

    protected function fakeResponse():Response
    {
        return new Response();
    }
}
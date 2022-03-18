<?php

namespace App\Common\HttpTracker;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Tracker\PointContext;

class Index extends PointContext
{
    public function __construct($config = [])
    {
        $this->enableAutoSave()->setSaveHandler(new SaveHandler($config));
    }

    public static function startArgsRequest(Request $request, array $merge = [])
    {
        $args = array_merge([
            'url' => $request->getUri()->__toString(),
            'ip' => \Linkunyuan\EsUtility\ip($request),
            'method' => $request->getMethod(),
            'header' => $request->getHeaders(),
//            'server' => $request->getServerParams(),
            'GET' => $request->getQueryParams(),
            'POST' => $request->getParsedBody() ?: json_decode($request->getBody()->__toString(), true),
            'path' => $request->getUri()->getPath(),
            'server_name' => 'Joyboo-server'
        ], $merge);
        krsort($args, SORT_STRING);
        return $args;
    }

    public static function endArgsResponse(Response $response, array $merge = [])
    {
        $data = $response->getBody()->__toString();
        if (is_string($data) && ($array = json_decode($data, true)))
        {
            $data = $array;
        }
        return self::endArgs($response->getStatusCode(), $data, $merge);
    }

    public static function endArgs($httpCode, $data, array $merge = [])
    {
        return ['httpStatusCode' => $httpCode, 'data' => $data] + $merge;
    }
}

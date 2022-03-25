<?php

namespace App\Model;

use EasySwoole\HttpClient\Bean\Response;
use EasySwoole\HttpClient\HttpClient;

class HttpTracker extends Base
{
    protected $tableName = 'http_tracker';

    // user-agent的复发标识
    const REPEAT_SYSBOL = ';JoybooHttpTracker';

    /** @var bool|string 是否开启时间戳 */
    protected  $autoTimeStamp = true;
    /** @var bool|string 创建时间字段名 false不设置 */
    protected  $createTime = 'instime';
    /** @var bool|string 更新时间字段名 false不设置 */
    protected  $updateTime = false;

    public $sort = ['instime' => 'desc'];

    // starttime和endtime是小数点后有四位的时间戳字符串,转为int毫秒时间戳
    protected function _trackerTime($timeStamp)
    {
        return $timeStamp ? intval($timeStamp * 1000) : $timeStamp;
    }

    protected function getStartTimeAttr($val)
    {
        return $this->_trackerTime($val);
    }

    protected function getEndTimeAttr($val)
    {
        return $this->_trackerTime($val);
    }

    protected function getUrlAttr($val)
    {
        return urldecode($val);
    }

    protected function getRequestAttr($val)
    {
        $arr = [];
        if ($json = json_decode($val, true))
        {
            $arr = $json;
        }
        return arrayToStd($arr);
    }
    protected function getResponseAttr($val)
    {
        $json = json_decode($val, true);
        $arr = is_array($json) ? $json : [];
        return arrayToStd($arr);
    }

    /**
     * @return Response|null
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function repeatOne():? Response
    {
        $data = $this->toRawArray();
        $url = $data['url'];
        $request = json_decode($data['request'], true);

        $HttpClient = new HttpClient($url);
        if (stripos($url, 'https://') === 0)
        {
            $HttpClient->setEnableSSL();
        }
        // 禁止重定向
        $HttpClient->setFollowLocation(0);

        $headers = [];
        foreach ($request['header'] as $hk => $hd) {
            if (is_array($hd)) {
                $hd = current($hd);
            }
            $headers[$hk] = $hd;
        }

        // UserAgent区分复发请求
        $headers['user-agent'] = ($headers['user-agent'] ?? '') . self::REPEAT_SYSBOL;

        $HttpClient->setHeaders($headers, true, false);

        $method = strtolower($request['method']);

        /** @var Response $response */
        $response = null;
        // 不需要body的方法
        if (in_array($method, ['get', 'head', 'trace', 'delete']))
        {
            $response = $HttpClient->$method();
        }
        elseif (in_array($method, ['post', 'patch', 'put', 'download']))
        {
            $response = $HttpClient->$method($request[strtoupper($method)] ?? []);
        }

        if ($response)
        {
            $body = $response->getBody();
            $json = json_decode($body, true);
            if ($response->getStatusCode() !== 200 || $json['code'] !== 200)
            {
                trace("复发请求失败，返回BODY: {$body}，参数为：" . json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        }

        return $response;
    }
}

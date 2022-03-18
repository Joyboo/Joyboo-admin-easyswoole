<?php

namespace App\CustomProcess;

class HttpTracker extends Base
{
    /**
     * 消费链路追踪日志
     * @param $data
     * @return mixed|void
     */
    protected function consume($data = '')
    {
        try {

            $data = json_decode($data, true);

            if (empty($data['pointId']))
            {
                return ;
            }

            $ip = isset($data['startArg']['ip']) ? $data['startArg']['ip'] : ($data['startArg']['post']['ip'] ?? '');

            $request = [];
            $startArg = $data['startArg'] ?? [];
            foreach ($startArg as $rqKey => $rkValue)
            {
                if (!in_array($rqKey, ['ip', 'url', 'server_name', 'repeat']))
                {
                    if (is_string($rkValue) && ($rkJson = json_decode($rkValue, true)))
                    {
                        $rkValue = $rkJson;
                    }
                    $request[$rqKey] = $rkValue;
                }
            }

            $request = json_encode($request, JSON_UNESCAPED_UNICODE);
            $response = json_encode($data['endArg'] ?? [], JSON_UNESCAPED_UNICODE);

            $startTime = $data['startTime'] ?? '';
            $endTime = $data['endTime'] ?? '';
            $runtime = 0;
            if ($startTime && $endTime)
            {
                // tracker时间是秒级小数点后有四位, 转为整数毫秒级
                $t = 10000;
                $runtime = intval((($endTime * $t) - ($startTime * $t)) / 10);
            }

            $insert = [
                'point_id' => $data['pointId'],
                'parent_id' => $data['parentId'] ?? '',
                'point_name' => $data['pointName'],
                'is_next' => intval($data['isNext']),
                'depth' => $data['depth'],
                'repeat' => $data['startArg']['repeat'] ?? 0,
                'status' => $data['status'],
                'ip' => $ip,
                'url' => $data['startArg']['url'] ?? '',
                'request' => $request,
                'response' => $response,
                'server_name' => $data['startArg']['server_name'] ?? '',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'runtime' => $runtime
            ];

            /** @var \App\Model\HttpTracker $model */
            $model = model('HttpTracker');
            $model->data($insert)->save();
        }
        catch (\Throwable | \Exception $e)
        {
            trace($e->__toString(), 'error');
        }
    }
}

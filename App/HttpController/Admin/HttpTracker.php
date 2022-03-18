<?php

namespace App\HttpController\Admin;

use EasySwoole\Mysqli\QueryBuilder;

class HttpTracker extends Auth
{
    protected function _search()
    {
        return function (QueryBuilder $builder) {
            $filter = $this->filter();
            $builder->where('instime', [$filter['begintime'], $filter['endtime']], 'between');

            // envkey: {"one":"point_name","two":"point_id"}
            // envvalue: {"one":"123","two":"4556"}
            foreach (['envkey', 'envvalue'] as $col)
            {
                if (!empty($filter[$col])) {
                    $filter[$col] = json_decode($filter[$col], true);
                }
            }

            if (!empty($filter['envkey']))
            {
                foreach ($filter['envkey'] as $key => $value)
                {
                    if ($like = $filter['envvalue'][$key])
                    {
                        $calc = true;
                        // 支持逻辑运算转换为like
                        $symbol = ['&&' => ' AND ', '||' => ' OR '];
                        foreach ($symbol as $sym => $join)
                        {
                            if (strpos($like, $sym) !== false)
                            {
                                $tmp = [];
                                $arr = explode($sym, $like);
                                foreach ($arr as $item)
                                {
                                    $item && $tmp[] = "$value LIKE '%{$item}%'";
                                }
                                if ($tmp) {
                                    $tmp = implode($join, $tmp);
                                    $builder->where("($tmp)");
                                    $calc = false;
                                }
                            }
                        }
                        if ($calc) {
                            $builder->where($value, "%{$like}%", 'LIKE');
                        }
                    }
                }
            }

            $runtime = $filter['runtime'] ?? 0;
            if ($runtime > 0)
            {
                $builder->where('runtime', $runtime, '>=');
            }
            /*
             * 生成的SQL分析示例
             * explain partitions SELECT SQL_CALC_FOUND_ROWS * FROM `http_tracker` WHERE  `instime` between 1646197200 AND 1647493199  AND `point_name` LIKE '%123%'  AND (point_id LIKE '%4556%' AND point_id LIKE '%789%') ORDER BY instime DESC  LIMIT 0, 100\G
             * */
        };
    }
}

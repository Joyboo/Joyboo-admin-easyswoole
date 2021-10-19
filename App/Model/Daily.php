<?php


namespace App\Model;


class Daily extends Base
{
    protected $connectionName = 'log';

    protected function buildWhere($filter = [])
    {
        $where = [];
        $operinfo = $_SERVER[config('SERVER_EXTRA.operinfo')];

        if (isset($filter['tzn'])) {
            $where['timezone'] = $filter['tzn'];
        }
        if (isset($filter['gameid']) && $filter['gameid'] !== '') {
            $where['gameid'] = [is_string($filter['gameid']) ? [$filter['gameid']] : $filter['gameid'], 'in'];
        }
        if (!isSuper($operinfo['rid'])) {
            $where['gameid'] = [$operinfo['extension']['gameids'], 'in'];
            $where['pkgbnd'] = [$operinfo['extension']['pkgbnd'], 'in'];
        }
        return $where;
    }

    /**
     * 营收
     */
    public function revenue($range, $filter = [])
    {
        switch ($range) {
            case 1: // 今天
                $begin = $end = date('ymd');
                break;
            case 2: // 昨天
                $begin = $end = date('ymd', strtotime('yesterday'));
                break;
            case 3: // 本月
                $begin = date('ym01');
                $end = date('ymt');
                break;
            case 4: // 上月
                $last = strtotime('-1 month');
                $begin = date('ym01', $last);
                $end = date('ymt', $last);
                break;
        }

        $where = ['ymd' => [[$begin, $end], 'between']];
        $where += $this->buildWhere($filter);

        $money = $this->where($where)->sum('money');
        return intval($money);
    }

    public function lastWeek($filter = [])
    {
        $lastWeekStart = date('ymd', strtotime('last week monday'));
        $lastWeekEnd = date('ymd', strtotime('last week sunday'));

        $where = ['ymd' => [[$lastWeekStart, $lastWeekEnd], 'between']];
        $where += $this->buildWhere($filter);

        $data = $this->where($where)
            ->field('ymd,sum(reg) as sum_reg,sum(login) as sum_login,sum(money) as sum_money,sum(persons) sum_persons')
            ->group('ymd')->order('ymd', 'asc')
            ->indexBy('ymd');

        return $this->weekly('last', $data ?? []);
    }

    public function thisWeek($filter = [])
    {
        $weekStart = date('ymd', strtotime('this week monday'));
        $weekEnd = date('ymd', strtotime('this week sunday'));

        $where = ['ymd' => [[$weekStart, $weekEnd], 'between']];
        $where += $this->buildWhere($filter);

        $data = $this->where($where)
            ->field('ymd,sum(reg) as sum_reg,sum(login) as sum_login,sum(money) as sum_money,sum(persons) sum_persons')
            ->group('ymd')->order('ymd', 'asc')
            ->indexBy('ymd');
        return $this->weekly('week', $data ?? []);
    }

    protected function weekly($key, $data = [])
    {
        $result = $reg = $dau = $money = $ffl = $arppu = $arpu = [];
        foreach ($data as $ymd => $value)
        {
            $reg[] = $value['sum_reg'] ?? 0;
            $dau[] = $value['sum_login'] ?? 0;
            $money[] = $value['sum_money'] ?? 0;
            $ffl[] = $value['sum_login'] ? sprintf('%.2f', $value['sum_persons'] / $value['sum_login'] * 100) : 0;
            $arppu[] = $value['sum_persons'] ? sprintf('%.2f', $value['sum_money'] / $value['sum_persons']) : 0;
            $arpu[] = $value['sum_login'] ? sprintf('%.2f', $value['sum_money'] / $value['sum_login']) : 0;
        }
        foreach (['reg', 'dau', 'money', 'ffl', 'arppu', 'arpu'] as $col)
        {
            $result[$col][$key] = $$col;
        }
        return $result;
    }
}

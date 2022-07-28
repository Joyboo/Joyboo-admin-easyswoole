<?php


namespace App\HttpController\Admin;

use Swoole\Table;
use WonderGame\EsUtility\HttpController\Admin\SysinfoTrait;
use WonderGame\EsUtility\Common\Classes\FdManager;

/**
 * Class Sysinfo
 * @property \App\Model\Admin\Sysinfo $Model
 * @package App\HttpController\Admin
 */
class Sysinfo extends Auth
{
    protected array $_authAlias = ['showSwooleTable' => 'index'];

    use SysinfoTrait;

    public function showSwooleTable()
    {
        $Fdmanager = FdManager::getInstance();
        $tables = $Fdmanager->getTableAll();
        $array = [];
        /** @var Table $table */
        foreach ($tables as $tbname => $table)
        {
            $tmp = [
                'TableName' => $tbname,
                'Size' => $table->getSize(),
                'MemorySize' => memory_convert($table->getMemorySize()),
                'Count' => $table->count(),
            ];
            foreach ($table as $key => $row)
            {
                $tmp['Rows'][$key] = $row;
            }
            $array[] = $tmp;
        }
        $this->success($array);
    }
}

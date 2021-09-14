<?php


namespace EasySwoole\Config;


use EasySwoole\Spl\SplArray;
use Swoole\Table;

class TableConfig extends AbstractConfig
{
    private $table;

    function __construct(int $size = 1024,int $dataSize = 4096)
    {

        $this->table = new Table($size);
        $this->table->column('data', Table::TYPE_STRING, $dataSize);
        $this->table->create();
    }

    function getConf($key = null)
    {
        if ($key == null) {
            $data = [];
            foreach ($this->table as $key => $item) {
                $data[$key] = unserialize($item['data']);
            }
            return $data;
        }else{
            $temp = explode(".", $key);
            $data = $this->table->get(array_shift($temp));
            if(!$data){
                return null;
            }
            $data = unserialize($data['data']);
            if(!empty($temp) && is_array($data)){
                $data = new SplArray($data);
                return $data->get(implode('.', $temp));
            }else{
                return $data;
            }
        }
    }

    function setConf($key, $val): bool
    {
        if (strpos($key, ".") > 0) {
            $temp = explode(".", $key);
            $key = array_shift($temp);
            $data = $this->getConf($key);
            if (is_array($data)) {
                $data = new SplArray($data);
            } else {
                $data = new SplArray();
            }
            $data->set(implode('.', $temp), $val);
            return $this->table->set($key, [
                'data' => serialize($data->getArrayCopy())
            ]);
        } else {
            return $this->table->set($key, [
                'data' => serialize($val)
            ]);
        }
    }

    function load(array $array): bool
    {
        $this->clear();
        foreach ($array as $key => $value) {
            $this->setConf($key, $value);
        }
        return true;
    }

    function merge(array $array): bool
    {
        foreach ($array as $key => $value) {
            $data = $this->getConf($key);
            if (is_array($data)) {
                $data = $value + $data;
            } else {
                $data = $value;
            }
            $this->setConf($key, $data);
        }
        return true;
    }

    function clear(): bool
    {
        $keys = [];
        foreach ($this->table as $key => $item) {
            $keys[] = $key;
        }
        foreach ($keys as $key){
            $this->table->del($key);
        }
        return true;
    }

    function storage():Table
    {
        return $this->table;
    }
}
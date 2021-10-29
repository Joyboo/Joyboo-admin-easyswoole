<?php

namespace App\Common\Classes;

use Vtiful\Kernel\Excel;
use Vtiful\Kernel\Format;

/**
 *
 * XlsWriter官方文档：https://xlswriter-docs.viest.me/zh-cn/kuai-su-shang-shou/reader
 * 来自easyswoole官方的推荐 http://www.easyswoole.com/OpenSource/xlsWriter.html
 *
 * 支持游标模式的超大文件读取，内存消耗不到1MB，需安装XlsWriter.so扩展
 *
 * Class XlsWriter
 */
class XlsWriter
{
    const TYPE_INT = Excel::TYPE_INT;
    const TYPE_STRING = Excel::TYPE_STRING;
    const TYPE_DOUBLE = Excel::TYPE_DOUBLE;
    const TYPE_TIMESTAMP = Excel::TYPE_TIMESTAMP;

    protected $excel = null;

    protected $offset = 0;

    protected $setType = [];

    protected $config = [];

    public function __construct($path = '')
    {
        if (empty($path))
        {
            $path = config('export_dir');
        }
        if (!is_dir($path))
        {
//            mkdir($path, 0777, true);
            throw new \Exception('没有这个目录：' . $path);
        }

        $this->setConfig(['path' => $path]);
        $this->excel = new Excel($this->getConfig());
    }

    public function setConfig($config = [])
    {
        $this->config = array_merge_multi($this->config, $config);
    }

    public function getConfig($name = null)
    {
        if (!is_null($name))
        {
            return $this->config[$name] ?? null;
        }
        return $this->config;
    }

    /**
     * 设置读取参数
     * @param int $offset 偏移量，传1会丢弃第一行，传2会丢弃第一行和第二行 ...
     * @param array $setType 列单元格数据类型，从0开始 [2 => \XlsWriter::TYPE_TIMESTAMP]表示第三列的单元格是时间类型
     * @return $this
     */
    public function setReader(int $offset = 0,array $setType = [])
    {
        $this->offset = $offset;
        $this->setType = $setType;
        return $this;
    }

    /**
     * 导入，游标模式
     * @param $file
     * @param callable $callback function(int $row, int $cell, $data)
     */
    public function readFileByCursor($file, callable $callback)
    {
        $sheetList = $this->excel->openFile($file)->sheetList();

        foreach ($sheetList as $sheetName)
        {
            $sheet = $this->excel->openSheet($sheetName);
            if ($this->offset > 0)
            {
                $sheet->setSkipRows($this->offset);
            }
            if ($this->setType)
            {
                $sheet->setType($this->setType);
            }
            $sheet->nextCellCallback($callback);
        }
    }

    /**
     * 导入，全量模式
     * @param $file
     */
    public function readFile($file)
    {
        $sheetList = $this->excel->openFile($file)->sheetList();

        $result = [];
        foreach ($sheetList as $sheetName)
        {
            $sheet = $this->excel->openSheet($sheetName);
            if ($this->offset > 0)
            {
                $sheet->setSkipRows($this->offset);
            }
            if ($this->setType)
            {
                $sheet->setType($this->setType);
            }
            $sheetData = $sheet->getSheetData();
            $result = array_merge($result, $sheetData);
            unset($sheetData, $sheet);
        }

        return $result;
    }

    /**
     * 导出，全量模式
     * @param $file
     * @param array $data
     * @param array $header
     */
    public function ouputFile($file, $data = [], $header = [])
    {
        $object = $this->excel->fileName($file);
        if ($header)
        {
            $object->header($header);
        }
        $object->data($data)->output();
    }

    /**
     * 导出，固定内存模式
     * @param string $file
     * @param array $header
     * @param array $data
     */
    public function ouputFileByCursor(string $file, array $header, array $data)
    {
        $suffix = '.xlsx';
        if (substr($file, -5) !== $suffix) {
            $file .= $suffix;
        }

        $thKeys = array_keys($header);
        $thValue = array_values($header);
        $result = [];
        // 过滤掉不在表头的字段
        foreach ($data as $key => &$value)
        {
            $row = [];
            // 因为data只能是索引数组，所以这里按顺序十分重要
            foreach ($thKeys as $col)
            {
                $row[] = $value[$col] ?? '';
            }
            $row && $result[] = $row;
            unset($data[$key]);
        }

        $fileObject = $this->excel->constMemory($file);
        $fileHandle = $fileObject->getHandle();
        $format = new Format($fileHandle);
        // 默认加粗，其他样式参考Format
        $boldStyle = $format->bold()->toResource();
        // 给表头设置样式
        $fileObject->setRow('A1', 10, $boldStyle)
            ->header($thValue)
            ->data($result)
            ->output();
    }
}

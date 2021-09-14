<?php


namespace App\Model;

use EasySwoole\ORM\AbstractModel;

abstract class Base extends AbstractModel
{
    public $gameid = '';

    protected $_error = [];

    protected $tableName = '';

    public function __construct($data = [], $tabname = '', $gameid = '')
    {
        if (empty($tabname)) {
            $tabname = $this->_getTable();
        }
        $this->tableName($tabname);

        $tabname != '' &&  $this->tableName($tabname);
        $this->gameid = $gameid;
        parent::__construct($data);
    }

    /**
     * 获取表名，并将将Java风格转换为C的风格
     * @return string
     */
    protected function _getTable()
    {
        $name = basename(str_replace('\\', '/', get_called_class()));
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }

    protected function getPk()
    {
        $this->schemaInfo()->getPkFiledName();
    }

    protected function getExtensionAttr($extension = '', $alldata = [])
    {
        return is_array($extension) ? $extension : json_decode($extension, true);
    }

    /**
     * 数据写入前对extension字段的值进行处理
     *
     * @access protected
     * @param array $extension 原数据
     * @param bool $encode 是否强制编码
     * @return string 处理后的值
     */
    protected function setExtensionAttr($extension = [], $alldata = [], $relation = [], $encode = true)
    {
        if (is_string($extension))
        {
            $extension = json_decode($extension, true);
            if (!$extension) {
                return '{}';
            }
        }
        return $encode ? json_encode($extension) : $extension;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function setError($err = [])
    {
        $this->_error = $err;
        return $this;
    }
}

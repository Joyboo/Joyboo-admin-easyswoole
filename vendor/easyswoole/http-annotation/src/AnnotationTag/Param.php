<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class Param
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Param extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $name;

    public $preHandler;

    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $from = [];


    public $defaultValue = null;

    /**
     * @var string
     */
    public $alias = null;

    /**
     * @var bool
     */
    public $deprecated;

    /**
     * 以下为校验规则
     */

    public $validateRuleList = [];

    private $allowValidateRule = [
        'required', 'notEmpty', 'optional',
        'activeUrl', 'alpha', 'alphaNum', 'alphaDash', 'between', 'bool',
        'decimal', 'dateBefore', 'dateAfter', 'equal', 'different',
        'equalWithColumn', 'differentWithColumn', 'lessThanWithColumn', 'greaterThanWithColumn',
        'func', 'inArray', 'notInArray', 'isIp',
        'integer', 'numeric', 'float', 'length', 'lengthMax', 'lengthMin', 'mbLength', 'mbLengthMax', 'mbLengthMin',
        'betweenLen', 'betweenMbLen', 'money', 'max', 'min', 'regex', 'allDigital',
        'timestamp', 'timestampBeforeDate', 'timestampAfterDate',
        'timestampBefore', 'timestampAfter', 'url', 'allowFile', 'allowFileType'
    ];

    /**
     * @var string
     */
    public $activeUrl;
    /**
     * @var string
     */
    public $alpha;
    /**
     * @var string
     */
    public $alphaNum;
    /**
     * @var string
     */
    public $alphaDash;
    /**
     * @var array
     */
    public $between;
    /**
     * @var string
     */
    public $bool;
    /**
     * @var string
     */
    public $decimal;
    /**
     * @var string
     */
    public $dateBefore;
    /**
     * @var string
     */
    public $dateAfter;
    /**
     * @var string
     */
    public $equal;
    /**
     * @var string
     */
    public $different;
    /**
     * @var string
     */
    public $equalWithColumn;
    /**
     * @var string
     */
    public $differentWithColumn;
    /**
     * @var string
     */
    public $lessThanWithColumn;
    /**
     * @var string
     */
    public $greaterThanWithColumn;
    /**
     * @var string
     */
    public $float;
    /**
     * @var string
     */
    public $func;
    /**
     * @var array
     */
    public $inArray;
    /**
     * @var string
     */
    public $integer;
    /**
     * @var string
     */
    public $isIp;
    /**
     * @var string
     */
    public $notEmpty;
    /**
     * @var string
     */
    public $numeric;
    /**
     * @var array
     */
    public $notInArray;
    /**
     * @var string
     */
    public $length;
    /**
     * @var string
     */
    public $mbLength;
    /**
     * @var string
     */
    public $lengthMax;
    /**
     * @var string
     */
    public $mbLengthMax;
    /**
     * @var string
     */
    public $lengthMin;
    /**
     * @var string
     */
    public $mbLengthMin;
    /**
     * @var array
     */
    public $betweenLen;
    /**
     * @var array
     */
    public $betweenMbLen;
    /**
     * @var string
     */
    public $money;
    /**
     * @var string
     */
    public $max;
    /**
     * @var string
     */
    public $min;
    /**
     * @var string
     */
    public $regex;
    /**
     * @var string
     */
    public $allDigital;
    /**
     * @var string
     */
    public $required;
    /**
     * @var string
     */
    public $timestamp;
    /**
     * @var string
     */
    public $timestampBeforeDate;
    /**
     * @var string
     */
    public $timestampAfterDate;
    /**
     * @var string
     */
    public $timestampBefore;
    /**
     * @var string
     */
    public $timestampAfter;
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $optional;

    /**
     * @var array
     */
    public $allowFile;

    /**
     * @var array
     */
    public $allowFileType;

    public function tagName(): string
    {
        return 'Param';
    }

    function __onParser()
    {
        foreach ($this->allowValidateRule as $ruleName) {
            if ($this->$ruleName !== null) {
                $this->validateRuleList[$ruleName] = $this->$ruleName;
                //对inArray 做特殊处理
                if (in_array($ruleName, ['inArray', 'notInArray', 'allowFile', 'allowFileType'])) {
                    if (!is_array($this->$ruleName[0])) {
                        $this->$ruleName = [$this->$ruleName];
                    }
                }
            }
        }
    }

    public function typeCast($val)
    {
        switch ($this->type) {
            case 'string':
            {
                return (string)$val;
            }
            case 'int':
            {
                return (int)$val;
            }
            case 'double':
            case 'real':
            case 'float':
            {
                return (float)$val;
            }
            case 'bool':
            {
                return (bool)$val;
            }
            case 'json':
            {
                return json_decode($val);
            }
            case 'array':
            {
                return (array)$val;
            }
            default:
            {
                return $val;
            }
        }
    }
}

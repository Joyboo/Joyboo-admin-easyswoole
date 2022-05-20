<?php


namespace App\Common\Languages;

use WonderGame\EsUtility\Common\Languages\Dictionary as BaseDictionary;

/**
 * 定义一个词典。
 * 值请务必于 const 变量名一致，这样是避免用户手敲词条名称出错
 * Class Dictionary
 * @package App\Languages
 */
class Dictionary extends BaseDictionary
{
    const HTTP_1 = 'HTTP_1';
    const HTTP_2 = 'HTTP_2';
}

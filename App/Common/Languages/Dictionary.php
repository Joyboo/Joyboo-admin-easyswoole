<?php


namespace App\Common\Languages;

use EasySwoole\I18N\AbstractDictionary;

/**
 * 定义一个词典。
 * 值请务必于 const 变量名一致，这样是避免用户手敲词条名称出错
 * Class Dictionary
 * @package App\Languages
 */
class Dictionary extends AbstractDictionary
{
    const SUCCESS = 'SUCCESS';
    const ERROR = 'ERROR';
    const FAIL = 'FAIL';

    const ADMIN_1 = 'ADMIN_1';
    const ADMIN_2 = 'ADMIN_2';
    const ADMIN_3 = 'ADMIN_3';
    const ADMIN_4 = 'ADMIN_4';
    const ADMID_5 = 'ADMIN_5';
    const ADMIN_6 = 'ADMIN_6';
    const ADMIN_7 = 'ADMIN_7';
    const ADMIN_8 = 'ADMIN_8';

    const HTTP_1 = 'HTTP_1';
    const HTTP_2 = 'HTTP_2';
}

<?php


namespace App\Common\Languages;

/**
 * 中文语言包
 * Class Chinese
 * @package App\Languages
 */
class Chinese extends Dictionary
{
    const SUCCESS = '成功';
    const ERROR = '错误';
    const FAIL = '失败';

    const ADMIN_1 = '请填写用户名';
    const ADMIN_2 = '用户名或密码错误';
    const ADMIN_3 = '登录成功';
    const ADMIN_4 = '您的账户已被锁定，请联系管理员';
    const ADMID_5 = 'jwt.uid错误';
    const ADMIN_6 = '操作失败';
    const ADMIN_7 = '参数错误';

    const HTTP_1 = '请登录';
    const HTTP_2 = '登录状态过期，请重新登录';
}

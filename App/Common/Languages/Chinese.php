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

    const BASECONTROLLERTRAIT_1 = '网络错误，请稍后再试';

    const ADMIN_ADMINTRAIT_3 = 'id为空';
    const ADMIN_ADMINTRAIT_6 = 'id错误';
    const ADMIN_ADMINTRAIT_7 = '旧密码不正确';
    const ADMIN_ADMINTRAIT_8 = 'id为空';
    const ADMIN_ADMINTRAIT_9 = 'id不正确或已被管理员封禁';

    const ADMIN_AUTHTRAIT_1 = '缺少token，请重新登录';
    const ADMIN_AUTHTRAIT_2 = 'token过期，请重新登录';
    const ADMIN_AUTHTRAIT_3 = '管理员id不正确';
    const ADMIN_AUTHTRAIT_4 = '管理员已被封禁';
    const ADMIN_AUTHTRAIT_5 = '请求方法错误';
    const ADMIN_AUTHTRAIT_6 = '添加失败';
    const ADMIN_AUTHTRAIT_7 = '缺少主键';
    const ADMIN_AUTHTRAIT_8 = '错误的主键';
    const ADMIN_AUTHTRAIT_9 = '编辑失败';
    const ADMIN_AUTHTRAIT_10 = '缺少主键';
    const ADMIN_AUTHTRAIT_11 = '错误的主键';
    const ADMIN_AUTHTRAIT_12 = '缺少主键';
    const ADMIN_AUTHTRAIT_13 = '错误的主键';
    const ADMIN_AUTHTRAIT_14 = '删除失败';
    const ADMIN_AUTHTRAIT_15 = '缺少id或column';
    const ADMIN_AUTHTRAIT_16 = '缺少主键';
    const ADMIN_AUTHTRAIT_17 = '错误的主键';
    const ADMIN_AUTHTRAIT_18 = '修改失败';

    const ADMIN_BASETRAIT_1 = '请选择游戏';

    const ADMIN_PUBTRAIT_1 = '请填写用户名';
    const ADMIN_PUBTRAIT_2 = '您的账户已被锁定，请联系管理员';
    const ADMIN_PUBTRAIT_3 = '登录成功';
    const ADMIN_PUBTRAIT_4 = '用户名或密码错误';

    const ADMIN_MENUTRAIT_1 = 'name重复';

    const HTTP_1 = '请登录';
    const HTTP_2 = '登录状态过期，请重新登录';

    const PERMISSION_DENIED = '对不起，没有权限';
    const PARAMS_ERROR = '参数错误';
}

<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\AuthTrait;

/**
 * Class Auth
 * @property \App\Model\Base $Model
 * @package App\HttpController\Admin
 */
abstract class Auth extends Base
{
    use AuthTrait;
}

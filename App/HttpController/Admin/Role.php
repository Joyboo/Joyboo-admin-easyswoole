<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\RoleTrait;

/**
 * Class Role
 * @property \App\Model\Admin\Role $Model
 * @package App\HttpController\Admin
 */
class Role extends Auth
{
    use RoleTrait;
}

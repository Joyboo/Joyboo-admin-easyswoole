<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\RoleTrait;

/**
 * Class Role
 * @property \App\Model\Role $Model
 * @package App\HttpController\Admin
 */
class Role extends Auth
{
    use RoleTrait;
}

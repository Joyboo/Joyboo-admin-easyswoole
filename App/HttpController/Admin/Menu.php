<?php


namespace App\HttpController\Admin;

use WonderGame\EsUtility\HttpController\Admin\MenuTrait;

/**
 * Class Menu
 * @property \App\Model\Admin\Menu $Model
 * @package App\HttpController\Admin
 */
class Menu extends Auth
{
    protected array $_authOmit = ['getMenuList', 'treeList'];

    use MenuTrait;
}

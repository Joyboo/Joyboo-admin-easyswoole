<?php


namespace App\Model;


class Expense extends Base
{
    public $sort = ['ymd' => 'desc', 'gameid' => 'desc', 'pkgbnd' => 'asc'];
}

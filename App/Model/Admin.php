<?php


namespace App\Model;

use WonderGame\EsUtility\Model\AdminModelTrait;

class Admin extends Base
{
    use AdminModelTrait;

    public function signInLog($data = [])
    {
        go(function () use ($data) {
            /** @var \App\Model\LogLogin $model */
            $model = model('LogLogin');
            $model->data($data)->save();
        });
    }
}

<?php


namespace App\Model\Admin;

use WonderGame\EsUtility\Model\Admin\AdminModelTrait;

class Admin extends Base
{
    use AdminModelTrait;

    public function signInLog($data = [])
    {
        go(function () use ($data) {
            /** @var \App\Model\Admin\LogLogin $model */
            $model = model_admin('LogLogin');
            $model->data($data)->save();
        });
    }
}

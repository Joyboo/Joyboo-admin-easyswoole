<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Admin;
use Linkunyuan\EsUtility\Classes\LamJwt;

abstract class Auth extends Base
{
    /**
     * 登录者（管理员）信息
     *
     * @var array
     * @access protected
     */
    protected $operinfo = [];

    protected function onRequest(?string $action): bool
    {
        return $this->checkAuthorization();
    }

    protected function checkAuthorization()
    {
        if (! $this->request()->hasHeader('authorization'))
        {
            $this->error(Code::ERROR_1, Dictionary::HTTP_1);
            return false;
        }

        $authorization = $this->request()->getHeader('authorization');
        if (is_array($authorization))
        {
            $authorization = current($authorization);
        }

        // jwt验证
        $jwt = LamJwt::verifyToken($authorization, config('auth.jwtkey'));
        $id = $jwt['data']['id'] ?? '';
        if ($jwt['status'] != 1 || empty($id))
        {
            $this->error(Code::ERROR_2, Dictionary::HTTP_2);
            return false;
        }

        // uid验证
        /** @var Admin $Admin */
        $Admin = model('Admin');
        // 当前用户信息
        $data = $Admin->where('id', $id)->get();
        if (empty($data))
        {
            $this->error(Code::ERROR_4, Dictionary::ADMID_5);
            return false;
        }
        // 关联的分组信息
        $relation = $data->relation ? $data->relation->toArray() : [];
        $this->operinfo = array_merge($data->toArray(), $relation);

        // 权限验证
        $this->checkAuth();

        return true;
    }

    protected function checkAuth()
    {
        // todo 权限验证
    }
}

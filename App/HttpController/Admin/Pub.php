<?php


namespace App\HttpController\Admin;

use App\Common\Languages\Dictionary;
use App\Model\LogLogin;
use Linkunyuan\EsUtility\Classes\LamJwt;

/**
 * Class Pub
 * @property \App\Model\Admin $Model
 * @package App\HttpController\Admin
 */
class Pub extends Base
{
    protected $modelName = 'Admin';

    public function index()
    {
        return $this->login();
    }

    public function login()
    {
        $array = $this->post;

        if (!isset($array['username']))
        {
            return $this->error(1004, Dictionary::ADMIN_1);
        }
        // 查询记录
        $data = $this->Model->where('username', $array['username'])->get();

        if ($data && password_verify($array['password'], $data['password'])) {
            $data = $data->toArray();

            if (empty($data['status']) && (!isSuper($data['rid']))) {
                return $this->error(1004, Dictionary::ADMIN_4);
            }

            /** @var LogLogin $LogLoginModel */
            $LogLoginModel = model('LogLogin');
            $LogLoginModel->data([
                'uid' => $data['id'],
                'name' => $data['realname'] ?: $data['username'],
                'ip' => ip($this->request()),
            ])->save();

            $token = LamJwt::getToken([
                'id' => $data['id'],
            ], config('auth.jwtkey'), config('auth.expire'));
            $this->success(['token' => $token]);

        } else {
            $this->error(1004, Dictionary::ADMIN_2);
        }
    }
}

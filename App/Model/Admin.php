<?php


namespace App\Model;

use App\Languages\Dictionary;
use Linkunyuan\EsUtility\Classes\LamJwt;
use EasySwoole\Http\Request;

class Admin extends Base
{
    /**
     * 用户登录处理
     * @param array $array 用户提交的数据（需要至少包括username和password字段）
     */
    public function login($array = [], Request $request)
    {
        if (!isset($array['username']))
        {
            $this->_error = ['code' => 510, 'msg' => lang(Dictionary::ADMIN_1)];
            return false;
        }
        // 查询记录
        $data = $this->where('username', $array['username'])->get();

        if ($data && password_verify($array['password'], $data['password']))
        {
            $data = $data->toArray();
            $id = $this->getPk();

            // 被锁定
            if (empty($data['extension']['status']) && (1 != $data['rid'] || 1 != $data[$id]))
            {
                $this->error = ['code' => 512, 'msg' => Dictionary::ADMIN_4];
                return false;
            }

            // 记录登录日志
            /** @var AdminLog $AdminLog */
            $AdminLog = model('AdminLog');
            $AdminLog->data([
                'uid' => $data['id'],
                'name' => $data['realname'] ?: $data['username'],
                'ip' => ip2long(ip($request)),
            ])->save();

            $token = LamJwt::getToken(['id' => $data['id']], config('auth.jwtkey'), config('auth.expire'));
            return ['token' => $token];
        }
        else
        {
            $this->_error = ['code' => 511, 'msg' => lang(Dictionary::ADMIN_2)];
            return false;
        }
    }

    /**
     * 新增登录日志
     * @param $data
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function addAdminLog($data)
    {

        $AdminLog->data([
            'uid' => $data['id'],
            'name' => $data['realname'] ?: $data['username'],
//            'ip' => request()->ip(1, true),
        ])->save();
        // 将登录id记录，后续更新
        return true;
    }
}

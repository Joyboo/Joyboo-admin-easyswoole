<?php


namespace App\Model;

use App\Common\Languages\Dictionary;
use App\Common\Exception\HttpParamException;
use EasySwoole\Mysqli\QueryBuilder;
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
            throw new HttpParamException(Dictionary::ADMIN_1);
        }
        // 查询记录
        $data = $this->where('username', $array['username'])->get();

        if ($data && password_verify($array['password'], $data['password']))
        {
            $data = $data->toArray();

            // 被锁定
            if (empty($data['extension']['status']) && (1 != $data['rid'] || 1 != $data['id']))
            {
                throw new HttpParamException(Dictionary::ADMIN_4);
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
            throw new HttpParamException(Dictionary::ADMIN_2);
        }
    }

    /**
     * 关联Role分组模型
     * @return array|mixed|null
     * @throws \Throwable
     */
    public function relation()
    {
        $callback = function(QueryBuilder $query){
            $query->where('status', 1);
            return $query;
        };

        return $this->hasOne(Role::class, $callback, 'rid', 'id');
    }
}

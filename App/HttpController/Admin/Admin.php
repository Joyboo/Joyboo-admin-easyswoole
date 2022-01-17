<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Daily;
use EasySwoole\ORM\Db\MysqliClient;
use EasySwoole\ORM\DbManager;
use Linkunyuan\EsUtility\Classes\LamJwt;

/**
 * Class Admin
 * @property \App\Model\Admin $Model
 * @package App\HttpController\Admin
 */
class Admin extends Auth
{
    protected $_uckAction = 'getUserInfo,getPermCode';

    protected function _search()
    {
        $where = [];
        if (!empty($this->get['rid']))
        {
            $where['rid'] = $this->get['rid'];
        }
        foreach (['username', 'realname'] as $val)
        {
            if (!empty($this->get[$val]))
            {
                $where[$val] = ["%{$this->get[$val]}%", 'like'];
            }
        }
        return $where;
    }

    protected function _afterIndex($items, $total)
    {
        /** @var \App\Model\Role $Role */
        $Role = model('Role');
        $roleList = $Role->getRoleListAll();
        foreach ($items as &$value)
        {
            unset($value['password']);
            $value->relation;
        }
        return parent::_afterIndex(['items' => $items, 'roleList' => $roleList], $total);
    }

    /**
     * 分析页
     */
    public function dashboardAnalysis()
    {
        DbManager::getInstance()->invoke(function (MysqliClient $client) {
            $filter = $this->filter();

            $result = $chart = [];
            /** @var Daily $Daily */
            $Daily = model('Daily');
            $Daily::invoke($client);

            $result['revenue'] = [
                // 今天
                'today' => $Daily->revenue(1, $filter),
                // 昨天
                'yesterday' => $Daily->revenue(2, $filter),
                // 本月
                'month' => $Daily->revenue(3, $filter),
                // 上月
                'lastmonth' => $Daily->revenue(4, $filter)
            ];


            // 上周
            $last = $Daily->lastWeek($filter);
            // 本周
            $week = $Daily->thisWeek($filter);

            $result['chart'] = array_merge_multi($last, $week);

            $this->success($result);
        });
    }

    /**
     * 工作台
     */
    public function dashboardWorkbench()
    {

    }

    public function getUserInfo()
    {
        // 客户端进入页,应存id
        if (!empty($this->operinfo['extension']['homePath']))
        {
            $Tree = new \App\Common\Classes\Tree();
            $homePage = $Tree->originData(['type' => [[0, 1], 'in']])->getHomePath($this->operinfo['extension']['homePath']);
        }
        $avatar = $this->operinfo['avatar'] ?? '';

        $result = [
            'id' => $this->operinfo['id'],
            'username' => $this->operinfo['username'],
            'realname' => $this->operinfo['realname'],
            'avatar' => $avatar,
            'desc' => $this->operinfo['desc'] ?? '',
            'homePath' => $homePage ?? '',
            'roles' => [
                [
                    'roleName' => $this->operinfo['role']['name'] ?? '',
                    'value' => $this->operinfo['role']['value'] ?? ''
                ]
            ]
        ];

        $this->success($result, Dictionary::SUCCESS);
    }

    /**
     * 用户权限码
     */
    public function getPermCode()
    {
        /** @var \App\Model\Menu $model */
        $model = model('Menu');
        $code = $model->permCode($this->operinfo['rid']);
        $this->success($code, Dictionary::SUCCESS);
    }

    protected function addGet()
    {
        $result = $this->_views();
        $this->success($result);
    }

    protected function _afterEditGet($items)
    {
        $result = $this->_views();

        unset($items['password'], $items['instime'], $items['itime']);
        $result['result'] = $items;

        return $result;
    }

    protected function _views()
    {
//        \Swoole\Coroutine::sleep(3);
        $result = [];
        // 角色组，菜单
        /** @var \App\Model\Role $Role */
        $Role = model('Role');
        /** @var \App\Model\Menu $Menu */
        $Menu = model('Menu');
        $roleAll = $Role->getRoleListAll();

        $roleList = $checkByRid = [];
        foreach ($roleAll as $value)
        {
            $roleList[] = ['label' => $value['name'], 'value' => $value['id']];
            if ($this->isSuper($value['id']))
            {
                $checkByRid[$value['id']] = true;
            } else {
                $arr = [];
                $menu = explode(',', $value['menu']);
                foreach ($menu as $val)
                {
                    if ($val !== '')
                    {
                        $arr[] = intval($val);
                    }
                }
                $checkByRid[$value['id']] = $arr;
            }
        }
        $result['roleList'] = $roleList;
        // 区分两种Tree数据，一种仅显示菜单，提供给homePath选中，一种是所有的权限展示
        $result['menuList'] = $Menu->menuList();
        $result['roleAuth'] = $Menu->menuAll();
        // 每个角色组有哪些权限
        $result['checkByRid'] = $checkByRid;
        return $result;
    }

    protected function _writeBefore()
    {
        // 留空，不修改密码
        if (empty($this->post['password'])) {
            unset($this->post['password']);
        }
    }

    protected function editPost()
    {
        if (!$this->isSuper())
        {
            $id = $this->post['id'];
            if (empty($id)) {
                $this->error(Code::ERROR, Dictionary::ADMIN_7);
            }
            $origin = $this->Model->where('id', $id)->val('extension');

            /**
             * 和数据库对比，如果原来已分配的包，当前操作用户没有这个包的权限，要追加进post
             * @param $current 当前操作用户的gameids或pkgbnd
             * @param $org 数据库原值，gameid或pkgbnd
             * @param $post $this->post[xxx]
             */
            $diffAuth = function ($current, $org, $post) {
                is_string($current) && $current = explode(',', $current);
                is_string($org) && $org = explode(',', $org);
                is_string($post) && $post = explode(',', $post);

                $result = [];
                foreach ($org as $value)
                {
                    if (!in_array($value, $current)) { $result[] = $value; }
                }

                return array_unique(array_merge($post, $result));
            };

            // 包权限
            $this->post['extension']['pkgbnd'] = $diffAuth(
                $this->operinfo['extension']['pkgbnd'],
                $origin['pkgbnd'],
                $this->post['extension']['pkgbnd']
            );
            // 游戏权限
            $this->post['extension']['gameids'] = $diffAuth(
                $this->operinfo['extension']['gameids'],
                $origin['gameids'],
                $this->post['extension']['gameids']
            );
        }

        parent::editPost();
    }

    public function getToken()
    {
        // 此接口比较重要，只允许超级管理员调用
        if (! $this->isSuper())
        {
            return $this->error(Code::CODE_FORBIDDEN);
        }
        if (!isset($this->get['id']))
        {
            return $this->error(Code::ERROR_3, Dictionary::ADMIN_7);
        }
        $id = $this->get['id'];
        $isExtsis = $this->Model->where(['id' => $id, 'status' => 1])->count();
        if (!$isExtsis)
        {
            return $this->error(Code::ERROR_4, Dictionary::ADMIN_7);
        }
        $token = LamJwt::getToken(['id' => $id], config('auth.jwtkey'), 3600);
        $this->success($token);
    }

    public function modify()
    {
        $userInfo = $this->operinfo;

        if ($this->isMethod('get'))
        {
            // role的关联数据也可以不用理会，ORM会处理
            unset($userInfo['password'], $userInfo['role']);
            // 默认首页treeSelect
            /** @var \App\Model\Menu $Menu */
            $Menu = model('Menu');
            $menuList = $Menu->menuList();
            $this->success(['menuList' => $menuList, 'result' => $userInfo]);
        }
        elseif ($this->isMethod('post'))
        {
            $id = $this->post['id'];
            if (empty($id) || $userInfo['id'] != $id)
            {
                // 仅允许管理员编辑自己的信息
                return $this->error(Code::ERROR, Dictionary::ERROR);
            }

            if ($this->post['__password'] && ! password_verify($this->post['__password'], $userInfo['password']))
            {
                return $this->error(Code::ERROR, '对不起，旧密码不正确');
            }

            parent::editPost();
        }
    }
}

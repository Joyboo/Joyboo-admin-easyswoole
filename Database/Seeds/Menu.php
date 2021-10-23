<?php

use EasySwoole\DatabaseMigrate\MigrateManager;
use EasySwoole\Mysqli\Client;

/**
 * filling data
 *
 * Class Menu
 */
class Menu
{
    protected $tableName = 'menu';

    /** @var Client $mysqliClient */
    protected $mysqliClient = null;

    public function __construct()
    {
        $this->mysqliClient = MigrateManager::getInstance()->getClient();
    }

    protected function insert($data = [])
    {
        $this->mysqliClient->queryBuilder()->insert($this->tableName, $data);
        $this->mysqliClient->execBuilder();
        return $this->mysqliClient->mysqlClient()->insert_id;
    }

    /**
     * 创建： php easyswoole migrate seed --create=Menu
     * 执行填充：php easyswoole migrate seed Menu
     * seeder run
     * @return void
     * @throws Throwable
     * @throws \EasySwoole\Mysqli\Exception\Exception
     */
    public function run()
    {
        // 首页
        $this->dashboard();
        // 系统管理及其子菜单、按钮
        $this->system();
        // 数据统计及其子菜单、按钮
//        $this->statistics();
        // APP设置
        $this->app();
        // 外部链接、内嵌链接
        $this->extlinks();
        // 日志
        $this->logs();
        // 广告投放
        $this->ad();
        // 关于
//        $this->about();
    }

    protected function dashboard()
    {
        $dashId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'sort' => 0,
            'name' => 'Dashboard',
            'title' => '首页',
            'icon' => 'ant-design:appstore-outlined',
            'path' => '/dashboard',
            'component' => 'LAYOUT',
            'redirect' => '/dashboard/analysis'
        ]);
        $this->insert([
            [
                'pid' => $dashId,
                'type' => 1,
                'name' => 'Analysis',
                'title' => '分析页',
                'sort' => 9,
                'path' => 'analysis',
                'component' => '/dashboard/analysis/index',
                'permission' => '/admin/dashboardAnalysis'
            ],
            [
                'pid' => $dashId,
                'type' => 1,
                'name' => 'Workbench',
                'title' => '工作台',
                'sort' => 10,
                'path' => 'workbench',
                'component' => '/dashboard/workbench/index',
                'permission' => '/admin/dashboardWorkbench'
            ]
        ]);
    }

    protected function system()
    {
        $systemId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'System',
            'title' => '系统管理',
            'sort' => 99,
            'icon' => 'ion:settings-outline',
            'path' => '/system',
            'component' => 'LAYOUT',
            'redirect' => '/system/account',
        ]);
        $accoutId = $this->insert([
            'pid' => $systemId,
            'type' => 1,
            'name' => 'AccountManagement',
            'title' => '账号管理',
            'sort' => 9,
            'path' => 'account',
            'component' => '/admin/system/account/index',
        ]);
        // 账号页面按钮权限
        $this->insert([
            [
                'pid' => $accoutId,
                'type' => 2,
                'title' => '添加账号',
                'permission' => '/account/add'
            ],
            [
                'pid' => $accoutId,
                'type' => 2,
                'title' => '编辑账号',
                'permission' => '/account/edit'
            ],
            [
                'pid' => $accoutId,
                'type' => 2,
                'title' => '删除账号',
                'permission' => '/account/del'
            ],
            [
                // 主要给开发者调试用
                'pid' => $accoutId,
                'type' => 2,
                'title' => '获取用户token',
                'permission' => '/admin/getToken'
            ]
        ]);

        $roleId = $this->insert([
            'pid' => $systemId,
            'type' => 1,
            'name' => 'RoleManagement',
            'title' => '角色管理',
            'sort' => 9,
            'path' => 'role',
            'component' => '/admin/system/role/index',
        ]);
        $this->insert([
            [
                'pid' => $roleId,
                'type' => 2,
                'title' => '添加角色',
                'permission' => '/role/add'
            ],
            [
                'pid' => $roleId,
                'type' => 2,
                'title' => '编辑角色',
                'permission' => '/role/edit'
            ],
            [
                'pid' => $roleId,
                'type' => 2,
                'title' => '删除角色',
                'permission' => '/role/del'
            ]
        ]);

        $menuId = $this->insert([
            'pid' => $systemId,
            'type' => 1,
            'name' => 'MenuManagement',
            'title' => '菜单管理',
            'sort' => 9,
            'path' => 'menu',
            'component' => '/admin/system/menu/index',
        ]);

        $this->insert([
            [
                'pid' => $menuId,
                'type' => 2,
                'title' => '添加菜单',
                'permission' => '/menu/add'
            ],
            [
                'pid' => $menuId,
                'type' => 2,
                'title' => '编辑菜单',
                'permission' => '/menu/edit'
            ],
            [
                'pid' => $menuId,
                'type' => 2,
                'title' => '删除菜单',
                'permission' => '/menu/del'
            ]
        ]);

        $sysId = $this->insert([
            'pid' => $systemId,
            'type' => 1,
            'name' => 'Sysinfo',
            'title' => '系统配置',
            'path' => 'sysinfo',
            'component' => '/admin/system/sysinfo/index',
        ]);

        $this->insert([
            [
                'pid' => $sysId,
                'type' => 2,
                'title' => '添加配置',
                'permission' => '/sysinfo/add'
            ],
            [
                'pid' => $sysId,
                'type' => 2,
                'title' => '编辑配置',
                'permission' => '/sysinfo/edit'
            ],
            [
                'pid' => $sysId,
                'type' => 2,
                'title' => '删除配置',
                'permission' => '/sysinfo/del'
            ],
        ]);

        $this->insert([
            'pid' => $systemId,
            'type' => 1,
            'name' => 'Modify',
            'title' => '个人设置',
            'path' => 'modify',
            'component' => '/admin/system/account/modify',
            'permission' => '/admin/modify'
        ]);

        $cronId = $this->insert([
            'pid' => $systemId,
            'type' => 1,
            'name' => 'Crontab',
            'title' => 'Cron管理',
            'path' => 'crontab',
            'component' => '/admin/system/crontab/index',
            'permission' => '/crontab/index'
        ]);

        $this->insert([
            [
                'pid' => $cronId,
                'type' => 2,
                'title' => '添加Cron',
                'permission' => '/crontab/add'
            ],
            [
                'pid' => $cronId,
                'type' => 2,
                'title' => '编辑Cron',
                'permission' => '/crontab/edit'
            ],
            [
                'pid' => $cronId,
                'type' => 2,
                'title' => '删除Cron',
                'permission' => '/crontab/del'
            ],
        ]);
    }

    protected function statistics()
    {
        $statisticsId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'Statistics',
            'title' => '数据统计',
            'sort' => 1,
            'icon' => 'ant-design:bar-chart-outlined',
            'path' => '/statistics',
            'component' => 'LAYOUT',
        ]);

        $this->insert([
            [
                'pid' => $statisticsId,
                'type' => 1,
                'name' => 'Daily',
                'title' => '日报',
                'component' => '/admin/statistics/daily/index',
                'permission' => '/statistics/daily'
            ],
            [
                'pid' => $statisticsId,
                'type' => 1,
                'name' => 'Ltv',
                'title' => 'LTV',
                'path' => 'ltv',
                'component' => '/admin/statistics/ltv/index',
                'permission' => '/statistics/ltv'
            ]
        ]);
    }

    protected function app()
    {
        $appId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'App',
            'title' => 'App设置',
            'sort' => 1,
            'icon' => 'ant-design:android-filled',
            'path' => '/app',
            'component' => 'LAYOUT',
        ]);

        $gameId = $this->insert([
            [
                'pid' => $appId,
                'type' => 1,
                'name' => 'Game',
                'title' => '游戏管理',
                'path' => 'game',
                'component' => '/admin/app/game/index',
                'permission' => '/game/index'
            ]
        ]);

        $this->insert([
            [
                'pid' => $gameId,
                'type' => 2,
                'title' => '添加游戏',
                'permission' => '/game/add'
            ],
            [
                'pid' => $gameId,
                'type' => 2,
                'title' => '编辑游戏',
                'permission' => '/game/edit'
            ],
            [
                'pid' => $gameId,
                'type' => 2,
                'title' => '删除游戏',
                'permission' => '/game/del'
            ],
            [
                // 批量分配
                'pid' => $gameId,
                'type' => 2,
                'title' => '分配给Ta',
                'permission' => '/game/give'
            ],
        ]);

        $pkgId = $this->insert([
            [
                'pid' => $appId,
                'type' => 1,
                'name' => 'PackageManagement',
                'title' => '包管理',
                'path' => 'package',
                'component' => '/admin/app/package/index',
                'permission' => '/package/index'
            ]
        ]);

        $this->insert([
            [
                'pid' => $pkgId,
                'type' => 2,
                'title' => '添加游戏',
                'permission' => '/package/add'
            ],
            [
                'pid' => $pkgId,
                'type' => 2,
                'title' => '编辑游戏',
                'permission' => '/packagegame/edit'
            ],
            [
                'pid' => $gameId,
                'type' => 2,
                'title' => '删除游戏',
                'permission' => '/package/del'
            ],
            [
                'pid' => $gameId,
                'type' => 2,
                'title' => '分配给Ta',
                'permission' => '/package/give'
            ],
        ]);
    }

    protected function extlinks()
    {
        $linkId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'Extlinks',
            'title' => '外链',
            'sort' => 9,
            'icon' => 'ant-design:pushpin-outlined',
            'path' => '/extlinks',
            'component' => 'LAYOUT',
            'isext' => 0,
        ]);

        $this->insert([
            [
                'pid' => $linkId,
                'type' => 1,
                'name' => 'Antdv',
                'title' => 'Ant Design Vue',
                'path' => 'https://2x.antdv.com/docs/vue/introduce-cn/',
                'isext' => 0,
            ],
            [
                'pid' => $linkId,
                'type' => 1,
                'name' => 'VbenDoc',
                'title' => 'Vben',
                'path' => 'https://vvbin.cn/doc-next/',
                'isext' => 0
            ],
            [
                'pid' => $linkId,
                'type' => 1,
                'name' => 'Vue3js',
                'title' => 'Vue3js',
                'path' => 'https://v3.cn.vuejs.org/',
                'isext' => 0
            ]
        ]);
    }

    protected function logs()
    {
        $logId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'Logs',
            'title' => '日志',
            'sort' => 98,
            'icon' => 'ant-design:file-outlined',
            'path' => '/logs',
            'component' => 'LAYOUT',
        ]);

        $this->insert([
            [
                'pid' => $logId,
                'type' => 1,
                'name' => 'AdminLog',
                'title' => '登录日志',
                'path' => 'adminlog',
                'component' => '/admin/logs/admin-log/index',
                'permission' => '/adminLog/index',
            ],
            [
                'pid' => $logId,
                'type' => 1,
                'name' => 'Log',
                'title' => '操作日志',
                'path' => 'log',
                'component' => '/admin/logs/log/index',
                'permission' => '/log/index',
            ],
            [
                'pid' => $logId,
                'type' => 1,
                'name' => 'MyErrorLog', // 因为vben已经自带一个ErrorLog组件，所以这里命名MyErrorLog
                'title' => '错误日志',
                'path' => 'errorlog',
                'component' => '/admin/logs/error-log/index',
                'permission' => '/errorLog/index'
            ]
        ]);
    }

    protected function ad()
    {
        $adId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'Ad',
            'title' => '广告管理',
            'icon' => 'ant-design:slack-outlined',
            'path' => '/ad',
            'component' => 'LAYOUT'
        ]);

        $expenseId = $this->insert([
            'pid' => $adId,
            'type' => 1,
            'name' => 'Expense',
            'title' => '投放消耗',
            'path' => 'expense',
            'component' => '/admin/ad/expense/index',
            'permission' => '/expense/index'
        ]);

        $this->insert([
            [
                'pid' => $expenseId,
                'type' => 2,
                'title' => '添加投放消耗',
                'permission' => '/expense/add'
            ],
            [
                'pid' => $expenseId,
                'type' => 2,
                'title' => '编辑投放消耗',
                'permission' => '/expense/edit'
            ],
            [
                'pid' => $expenseId,
                'type' => 2,
                'title' => '删除投放消耗',
                'permission' => '/expense/del'
            ],
        ]);
    }

    protected function about()
    {
        $abtId = $this->insert([
            'pid' => 0,
            'type' => 0,
            'name' => 'About',
            'title' => '关于',
            'icon' => 'simple-icons:about-dot-me',
            'path' => '/about',
            'component' => 'LAYOUT',
            'redirect' => '/about/index',
            'sort' => 100,
        ]);

        $this->insert([
            'pid' => $abtId,
            'type' => 1,
            'name' => 'AboutPage',
            'title' => '关于',
            'path' => 'index',
            'component' => '/sys/about/index',
            'permission' => '/about/index'
        ]);
    }
}

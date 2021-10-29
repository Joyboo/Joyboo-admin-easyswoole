<?php


namespace App\HttpController\Admin;

use App\Common\Classes\DateUtils;
use App\Common\Exception\HttpParamException;
use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Admin;
use EasySwoole\ORM\Db\MysqliClient;
use EasySwoole\Utility\MimeType;
use Linkunyuan\EsUtility\Classes\LamJwt;
use App\Common\Classes\Extension;
use EasySwoole\Http\Exception\FileException;

/**
 * Class Auth
 * @property \App\Model\Base $Model
 * @package App\HttpController\Admin
 */
abstract class Auth extends Base
{
    /**
     * 登录者（管理员）信息
     *
     * @var array
     * @access protected
     */
    protected $operinfo = [];

    protected $uploadKey = 'file';

    /**
     * 别名认证的操作
     * @var array
     */
    protected $_ckAliasAction = ['change' => 'edit', 'export' => 'index'];

    /**
     * 每个继承类可在此定义别名认证的操作
     * @var array
     */
    protected $_ckAction = [];

    /**
     * 无需认证的操作
     * 	@var string
     */
    protected $_uckSysAction = 'upload';

    /**
     * 每个继承类可在此定义无需认证的操作，格式为 操作1,操作2,....
     * @var string
     */
    protected $_uckAction = '';

    protected function onRequest(?string $action): bool
    {
        parent::onRequest($action);
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

        // 客户端版本
        /*if (!$this->checkClientVersion($jwt))
        {
            return false;
        }*/

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
        $super = config('SUPER_ROLE');
        if (empty($data['status']) && (!in_array($data['rid'], $super)))
        {
            $this->error(Code::ERROR, Dictionary::ADMIN_4);
            return false;
        }
        // 关联的分组信息
        $relation = $data->relation ? $data->relation->toArray() : [];
        $this->operinfo = $data->toArray();
        $this->operinfo['role'] = $relation;

        // 考虑移入Model获取器
        foreach (['gameids', 'pkgbnd'] as $col)
        {
            $colValue = $this->operinfo['extension'][$col] ?? [];
            if (is_string($colValue)) {
                $colValue = explode(',', $colValue);
            }
            $this->operinfo['extension'][$col] = $colValue;
        }

        $_SERVER[config('SERVER_EXTRA.operinfo')] = $this->operinfo;
        return $this->checkAuth();
    }

    /**
     * 检查客户端版本
     * @param array $jwt
     * @return bool
     */
    protected function checkClientVersion(array $jwt)
    {
        $sysinfo = config('sysinfo');
        $versionCode = [
            'version_later' => Code::VERSION_LATER,
            'version_force' => Code::VERSION_FORCE,
        ];
        foreach ($versionCode as $vKey => $code)
        {
            if (isset($jwt['data'][$vKey]) && isset($sysinfo[$vKey]) && $jwt['data'][$vKey] != $sysinfo[$vKey])
            {
                $this->error($code);
                return false;
            }
        }
        return true;
    }

    /**
     * 权限
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    protected function checkAuth()
    {
        if ($this->isSuper())
        {
            return true;
        }
        // /admin/admin/index
        $fullpath = $this->request()->getUri()->getPath();

        $this->_uckSysAction = trim($this->_uckSysAction, ',');
        $this->_uckSysAction = ',' . $this->_uckSysAction . ($this->_uckAction ? ",{$this->_uckAction}" : '' ) . ',';

        // 无需认证的操作
        $arr = explode('/', $fullpath);
        $method = end($arr);
        if(stripos($this->_uckSysAction, ",$method,") !== false)
        {
            return true;
        }

        /** @var \App\Model\Menu $Menu */
        $Menu = model('Menu');
        $priv = $Menu->where("permission<>'' and status=1")->field(['permission', 'id'])->indexBy('permission');

        // 无独立的权限菜单，但有别名认证的操作
        $path = substr($fullpath, strpos($fullpath, '/', 1));
        $arr = explode('\\', static::class);
        $className = strtolower(end($arr));

        $this->_uckAction = trim($this->_uckAction, ',');
        $this->_ckAliasAction = array_change_key_case(array_merge($this->_ckAliasAction, $this->_ckAction));

        // /index  index  /admin/index  admin/index 补全为统一格式: /admin/index
        $func = function ($val) use ($className) {
            $k = trim($val, '/');
            $count = substr_count($k, '/');

            // /index  index
            if ($count === 0)
            {
                return '/' . $className . '/' . $k;
            }
            // /admin/index  admin/index
            elseif ($count === 1)
            {
                return '/' . $k;
            }
            else {
                throw new \Exception('Error Auth _ckAliasAction: ' . $val);
            }
        };

        $alias = [];
        foreach ($this->_ckAliasAction as $key => $value)
        {
            $alias[$func($key)] = $func($value);
        }

        if (empty($priv[$path]) && array_key_exists($path, array_change_key_case($alias)))
        {
            $path = strtolower($alias[$path]);
        }

        if (empty($priv[$path]['id']))
        {
            $this->error(Code::CODE_FORBIDDEN);
            return false;
        }

        if (!in_array($priv[$path]['id'], $this->getUserMenus()))
        {
            $this->error(Code::CODE_FORBIDDEN);
            return false;
        }

        return true;
    }

    protected function setDbTimeZone(MysqliClient $client, $tzn)
    {
        $sql = "set time_zone = '$tzn';";
        trace($sql, 'info', 'sql');
        $client->rawQuery($sql);
    }

    protected function getDbTimeZone(MysqliClient $client, $debug = true)
    {
        $timeZone = $client->rawQuery("SHOW VARIABLES LIKE '%time_zone%'");
        if ($debug) {
            var_dump($timeZone);
        }
        return $timeZone;
    }

    protected function isSuper($rid = null)
    {
        if (is_null($rid)) {
            $rid = $this->operinfo['rid'];
        }
        return isSuper($rid);
    }

    protected function getUserMenus()
    {
        if ($this->isSuper())
        {
            return null;
        }
        $userMenu = explode(',', $this->operinfo['role']['menu'] ?? '');
        return is_array($userMenu) ? $userMenu : [];
    }

    public function add()
    {
        $this->info(__FUNCTION__);
    }

    public function edit()
    {
        $this->info(__FUNCTION__);
    }

    protected function info($name)
    {
        $rqm = $this->request()->getMethod();
        $method = $name . ucfirst(strtolower($rqm));

        try {
            $ref = new \ReflectionClass(static::class);
            $methosClass = $ref->getMethod($method);
            // 反射的是自己，this调用
            $this->{ $methosClass->name }();
        }
        catch (\ReflectionException $e)
        {
            $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }
        catch (HttpParamException $e)
        {
            $this->error(Code::ERROR, $e->getMessage());
        }
    }

    /**
     * 一般用来获取添加页需要的数据
     */
    protected function addGet()
    {
        return $this->success();
    }

    /**
     * 一般用来提交添加数据
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    protected function addPost()
    {
        $this->_writeBefore();
        $result = $this->Model->regOnQuery()->data($this->post)->save();
        $result ? $this->success() : $this->error(Code::ERROR);
    }

    protected function editPost()
    {
        $this->_writeBefore();
        $post = $this->post;
        $pk = $this->Model->getPk();
        if (!isset($post[$pk]))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }

        $model = $this->Model->where($pk, $post[$pk])->get();

        if (empty($model))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }

        /*
         * update返回的是执行语句是否成功,只有mysql语句出错时才会返回false,否则都为true
         * 所以需要getAffectedRows来判断是否更新成功
         */
        $upd = $model->regOnQuery()->update($post);
        if ($upd === false)
        {
            trace('edit update失败: ' . $model->lastQueryResult()->getLastError());
        }

        // 影响行数
        $rowCount = $model->lastQueryResult()->getAffectedRows();
        $rowCount ? $this->success() : $this->error(Code::ERROR, Dictionary::FAIL);
    }

    protected function editGet()
    {
        // todo 处理联合主键场景
        $pk = $this->Model->getPk();
        if (empty($this->get[$pk]))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }
        $model = $this->Model->where($pk, $this->get[$pk])->get();
        if (empty($model))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }
        $data = $this->_afterEditGet($model->toArray());
        $this->success($data);
    }

    public function del()
    {
        $get = $this->get;
        $pk = $this->Model->getPk();
        if (!isset($get[$pk]))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }

        $model = $this->Model->where($pk, $get[$pk])->get();
        if (empty($model))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }

        $result = $model->regOnQuery()->destroy();
        $result ? $this->success() : $this->error(Code::ERROR, Dictionary::ERROR);
    }

    public function change()
    {
        $post = $this->post;
        foreach (['id', 'column'] as $col)
        {
            if (!isset($post[$col]) || !isset($post[$post['column']]))
            {
                return $this->error(Code::ERROR_5, Dictionary::ADMIN_7);
            }
        }

        $column = $post['column'];

        $pk = $this->Model->getPk();
        if (!isset($post[$pk]))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }

        $model = $this->Model->where($pk, $post[$pk])->get();
        if (empty($model))
        {
            return $this->error(Code::ERROR, Dictionary::ADMIN_7);
        }

        $model->regOnQuery()->update([$column => $post[$column]]);
        $rowCount = $model->lastQueryResult()->getAffectedRows();
        $rowCount ? $this->success() : $this->error(Code::ERROR);
    }

    public function index()
    {
        $page = $this->get[config('fetchSetting.pageField')] ?? 1;          // 当前页码
        $limit = $this->get[config('fetchSetting.pageSize')] ?? 20;    // 每页多少条数据

        if ($where = $this->_search())
        {
            $this->Model->where($where);
        }

        // 处理排序
        $this->_order();

        $this->Model->scopeIndex();

        $model = $this->Model->limit($limit * ($page - 1), $limit)->withTotalCount();
        $items = $model->all();

        $result = $model->lastQueryResult();
        $total = $result->getTotalCount();

        // 后置操作
        $data = $this->_afterIndex($items, $total);
        $this->success($data);
    }

    protected function _order()
    {
        $sortField = $this->get['_sortField'] ?? ''; // 排序字段
        $sortValue = $this->get['_sortValue'] ?? ''; // 'ascend' | 'descend'

        $order = [];
        if ($sortField && $sortValue) {
            // 去掉前端的end后缀
//            $sortValue = substr($sortValue, 0, -3);
            $sortValue = str_replace('end', '', $sortValue);
            $order[$sortField] = $sortValue;
        }

        $this->Model->setOrder($order);
        return $order;
    }

    /**
     * 因为有超深级的JSON存在，如果需要导出全部，那么数据必须在后端处理，字段与前端一一对应
     * 有导出全部需求的菜单，不允许客户端field字段如extension.user.sid这样取值 或者 customRender 或者 插槽渲染, 否则导出全部时无法处理
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function export()
    {
        // 处理表头，客户端应统一处理表头
        $th = [];
        if ($thStr = $this->get[config('fetchSetting.exportThField')])
        {
            // _th=ymd=日期|reg=注册|login=登录

            $thArray = explode('|', urldecode($thStr));
            foreach ($thArray as $value)
            {
                list ($thKey, $thValue) = explode('=', $value);
                // 以表头key表准
                if ($thKey) {
                    $th[$thKey] = $thValue ?? '';
                }
            }
        }

        if ($where = $this->_search())
        {
            $this->Model->where($where);
        }

        // 处理排序
        $this->_order();

        // todo 使用fetch模式
        $items = $this->Model->all();
        $data = $this->_afterIndex($items, 0)[config('fetchSetting.listField')];

        // 是否需要合并合计行，如需合并，data为索引数组，为空字段需要占位

        // xlsWriter固定内存模式导出
        $excel = new \App\Common\Classes\XlsWriter();

        // 客户端response响应头获取不到Content-Disposition，用参数传文件名
        $fileName = $this->get[config('fetchSetting.exprotFilename')] ?? '';
        if (!empty($fileName))
        {
            $fileName = sprintf('export-%d-%s.xlsx', date(DateUtils::YmdHis), substr(uniqid(), -5));
        }

        $excel->ouputFileByCursor($fileName, $th, $data);
        $fullFilePath = $excel->getConfig('path') . $fileName;

        $this->response()->sendFile($fullFilePath);
//        $this->response()->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response()->withHeader('Content-Type', MimeType::getMimeTypeByExt('xlsx'));
//        $this->response()->withHeader('Content-Type', 'application/octet-stream');
        // 客户端获取不到这个header,待调试,文件名暂时用客户端传的
//        $this->response()->withHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $this->response()->withHeader('Cache-Control', 'max-age=0');
        $this->response()->end();

        // todo 下载完成就没有用了，延时删除掉
    }

    public function upload()
    {
        try {
            /** @var \EasySwoole\Http\Message\UploadFile $file */
            $file = $this->request()->getUploadedFile($this->uploadKey);

            // todo 文件校验
            $fileType = $file->getClientMediaType();

            $clientFileName = $file->getClientFilename();
            $arr = explode('.', $clientFileName);
            $suffix = end($arr);

            $dir = rtrim(config('UPLOAD.dir'), '/') . '/image/';
            // 当前控制器名做前缀
            $arr = explode('\\', static::class);
            $prefix = end($arr);
            $fileName = uniqid($prefix . '_', true) . '.' . $suffix;

            $fullPath = $dir . $fileName;
            $file->moveTo($fullPath);
//            chmod($fullPath, 0777);

            $url = '/image/' . $fileName;
            $this->writeUpload($url);
        }
        catch (FileException $e)
        {
            $this->writeUpload('', Code::ERROR, '上传失败: ' . $e->getMessage());
        }
    }

    public function unlink()
    {
//        $suffix = pathinfo($this->post['url'], PATHINFO_EXTENSION);
        $info = pathinfo($this->post['url']);
        $filename = $info['basename'];
        // todo 文件校验, 比如子类为哪个控制器，只允许删除此前缀的
        $suffix = $info['extension'];

        // 指定目录
        $dir = rtrim(config('UPLOAD.dir'), '/') . '/image/';

        $file = $dir . $filename;
        if (is_file($file))
        {
            @unlink($file);
        }
        $this->success();
    }

    /**
     * 构造查询数据
     * @return array
     */
    protected function _search()
    {
        return [];
    }

    /**
     * 公共参数,配合where使用
     * @return array
     */
    protected function filter()
    {
        $filter = [];

        // begintime, beginday
        $begintime = $this->get['begintime'] ?? '';
        if ($begintime || is_numeric($begintime))
        {
            $begintime = is_numeric($begintime) ? date('Y-m-d', $begintime <= 0 ? strtotime("$begintime days") : $begintime) : $begintime;
            $begintime = strtotime($begintime . (strpos($begintime, ':') !== false ? '' : ' 00:00:00'));
            $filter['begintime'] = $begintime;
            $filter['beginday'] = date('ymd', $begintime);
        }

        // endtime, endday
        if(isset($this->get['endtime']))
        {
            $endtime = $this->get['endtime'];
            $endtime = is_numeric($endtime) ? date('Y-m-d', $endtime < 0 ? strtotime("$endtime days") : $endtime) : $endtime;
            $endtime = strtotime($endtime . (strpos($endtime, ':') !== false ? '' : ' 23:59:59'));
            $filter['endtime'] = $endtime;
            $filter['endday'] = date('ymd', $endtime);
        }

        // tzn, tznSql
        if (isset($this->get['tzn']))
        {
            $tzn = $this->get['tzn'];
            foreach (config('sysinfo.region_domain.region') as $k => $v)
            {
                if ($v['tzn'] == $tzn)
                {
                    // 时间戳转换为选择时区的时间戳
                    foreach (['begintime', 'endtime'] as $t)
                    {
                        if (isset($filter[$t])) {
                            $filter[$t] = DateUtils::getTimeZoneStamp($filter[$t], $v['tzs']);
                        }
                    }
                    $filter['tznSql'] = ($tzn > 0 ? "+$tzn" : $tzn) . ':00';
                }
            }
            $filter['tzn'] = $tzn;
        }

        if (isset($this->get['gameid']))
        {
            $gameid = $this->get['gameid'];
            if (strpos($gameid, ',') !== false)
            {
                $gameid = explode(',', $gameid);
            }
            $filter['gameid'] = $gameid;
        }

        // ... 还有很多

        return $filter;
    }

    /**
     * 列表后置操作
     * @param $items
     * @return mixed
     */
    protected function _afterIndex($items, $total)
    {
        return [config('fetchSetting.listField') => $items, config('fetchSetting.totalField') => $total];
    }

    protected function _afterEditGet($data)
    {
        return $data;
    }

    protected function _writeBefore()
    {

    }
}

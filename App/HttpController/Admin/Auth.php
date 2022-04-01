<?php


namespace App\HttpController\Admin;

use App\Common\Classes\DateUtils;
use App\Common\Exception\HttpParamException;
use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Admin;
use EasySwoole\Component\Timer;
use EasySwoole\Policy\Policy;
use EasySwoole\Policy\PolicyNode;
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

    /////////////////////////////////////////////////////////////////////////
    /// 权限认证相关属性                                                    ///
    ///     1. 子类无需担心重写覆盖，校验时会反射获取父类属性值，并做合并操作     ///
    ///     2. 对于特殊场景也可直接重写 setPolicy 方法操作Policy              ///
    ///     3. 大小写不敏感                                                 ///
    /////////////////////////////////////////////////////////////////////////

    // 别名认证
    protected array $_authAlias = ['change' => 'edit', 'export' => 'index'];

    // 无需认证
    protected array $_authOmit = [];

    protected function onRequest(?string $action): bool
    {
        return parent::onRequest($action) && $this->checkAuthorization();
    }

    protected function checkAuthorization()
    {
        $tokenKey = config('TOKEN_KEY');
        if (! $this->request()->hasHeader($tokenKey))
        {
            $this->error(Code::ERROR_1, Dictionary::HTTP_1);
            return false;
        }

        $authorization = $this->request()->getHeader($tokenKey);
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

        // 管理员信息挂载到request
        $this->request()->withAttribute('operinfo', $this->operinfo);
        return $this->checkAuth();
    }

    /**
     * 权限认证
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

        $publicMethods = array_map('strtolower', array_keys($this->getAllowMethodReflections()));
        $currentAction = strtolower($this->getActionName());
        if (!in_array($currentAction, $publicMethods))
        {
            $this->error(Code::CODE_FORBIDDEN);
            return false;
        }
        $currentClassName = strtolower($this->getStaticClassName());
        $fullPath = "/$currentClassName/$currentAction";

        // 设置用户权限
        $userMenu = $this->getUserMenus();
        if (empty($userMenu))
        {
            $this->error(Code::CODE_FORBIDDEN);
            return false;
        }

        /** @var \App\Model\Menu $Menu */
        $Menu = model('Menu');
        $priv = $Menu->where('id', $userMenu, 'IN')->where('permission', '', '<>')->where('status', 1)->column('permission');
        if (empty($priv))
        {
            return true;
        }

        $policy = new Policy();
        foreach ($priv as $path)
        {
            $policy->addPath('/' . trim(strtolower($path), '/'));
        }


        $selfRef = new \ReflectionClass(self::class);
        $selfDefaultProtected = $selfRef->getDefaultProperties();
        $selfOmitAction = $selfDefaultProtected['_authOmit'] ?? [];
        $selfAliasAction = $selfDefaultProtected['_authAlias'] ?? [];

        // 无需认证操作
        if ($omitAction = array_map('strtolower', array_merge($selfOmitAction, $this->_authOmit)))
        {
            foreach ($omitAction as $omit)
            {
                in_array($omit, $publicMethods) && $policy->addPath("/$currentClassName/" . $omit);
            }
        }

        // 别名认证操作
        $aliasAction = array_change_key_case(array_map('strtolower', array_merge($selfAliasAction, $this->_authAlias)));
        if ($aliasAction && isset($aliasAction[$currentAction]))
        {
            $alias = trim($aliasAction[$currentAction], '/');
            if (strpos($alias, '/') === false)
            {
                if (in_array($alias, $publicMethods))
                {
                    $fullPath = "/$currentClassName/$alias";
                }
            } else {
                // 支持引用已有权限
                $fullPath = '/' . $alias;
            }
        }

        // 自定义认证操作
        $this->setPolicy($policy);

        $ok = $policy->check($fullPath) === PolicyNode::EFFECT_ALLOW;
        if (!$ok) {
            $this->error(Code::CODE_FORBIDDEN);
        }
        return $ok;
    }

    // 对于复杂场景允许自定义认证，优先级最高
    protected function setPolicy(Policy $policy)
    {

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
        $result = $this->Model->data($this->post)->save();
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
         *
         * 2022-03-18 edit 只要SQL没有错误就认为更新成功
         */
        $upd = $model->update($post);
        if ($upd === false)
        {
            trace('edit update失败: ' . $model->lastQueryResult()->getLastError(), 'error');
            $this->error(Code::ERROR, Dictionary::FAIL);
        } else {
            $this->success();
        }
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

        $result = $model->destroy();
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

        $upd = $model->update([$column => $post[$column]]);
//        $rowCount = $model->lastQueryResult()->getAffectedRows();
        $upd !== false ? $this->success() : $this->error(Code::ERROR);
    }

    public function index()
    {
        $page = $this->get[config('fetchSetting.pageField')] ?? 1;          // 当前页码
        $limit = $this->get[config('fetchSetting.sizeField')] ?? 20;    // 每页多少条数据

        $where = $this->_search();

        // 处理排序
        $this->_order();

        $this->Model->scopeIndex();

        $model = $this->Model->limit($limit * ($page - 1), $limit)->withTotalCount();
        $items = $model->all($where);

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

        $where = $this->_search();

        // 处理排序
        $this->_order();

        // todo 使用fetch模式
        $items = $this->Model->all($where);
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

        // 下载完成就没有用了，延时删除掉，异步非阻塞
        Timer::getInstance()->after(1000, function () use ($fullFilePath) {
            @unlink($fullFilePath);
        });
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
        return null;
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

        // ... other

        return $filter + $this->get;
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

    protected function _options($value, $label, $where = null)
    {
        $all = $this->Model->setOrder()->field([$label, $value])->all($where);

        $result = [];
        foreach ($all as $item)
        {
            $result[] = ['value' => $item[$value], 'label' => $item[$label]];
        }

        return $result;
    }
}

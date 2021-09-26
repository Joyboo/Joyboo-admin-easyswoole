<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Admin;
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
//        var_dump($this->operinfo);

        // 权限验证
        $this->checkAuth();

        return true;
    }

    protected function checkAuth()
    {
        // todo 权限验证
    }

    protected function isSuper()
    {
        $super = config('SUPER_ROLE');
        return in_array($this->operinfo['rid'], $super);
    }

    // todo 以下方法需要权限限制

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
        $data = $this->getSave($this->post);
        $result = $this->Model->data($data)->save();
        $result ? $this->success() : $this->error(Code::ERROR);
    }

    protected function editPost()
    {
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

        $data = $this->getSave($post, $model->toArray());

        /*
         * update返回的是执行语句是否成功,只有mysql语句出错时才会返回false,否则都为true
         * 所以需要getAffectedRows来判断是否更新成功
         */
        $upd = $model->update($data);
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
        $row = $this->getTemplate($model->toArray());
        $data = $this->_afterEditGet($row);
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
        foreach (['id', 'column', 'status'] as $col)
        {
            if (!isset($post[$col]))
            {
                return $this->error(Code::ERROR_5, Dictionary::ADMIN_7);
            }
        }

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

        $model->update([$post['column'] => $post['status']]);
        $rowCount = $model->lastQueryResult()->getAffectedRows();
        $rowCount ? $this->success() : $this->error(Code::ERROR);
    }

    public function index()
    {
        $page = $this->get['page'] ?? 1;          // 当前页码
        $limit = $this->get['pageSize'] ?? 20;    // 每页多少条数据

        if ($where = $this->_search())
        {
            $this->Model->where($where);
        }

        $this->Model->scopeIndex();

        $model = $this->Model->limit($limit * ($page - 1), $limit)->withTotalCount();
        $items = $model->all($where);

        $result = $model->lastQueryResult();
        $total = $result->getTotalCount();

        // 后置操作
        $items = $this->_afterIndex($items);
        $this->success(['items' => $items, 'total' => $total]);
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
            $fileName = uniqid('avatar_') . '.' . $suffix;

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

    /**
     * 构造查询数据
     * @return array
     */
    protected function _search()
    {
        return [];
    }

    /**
     * 列表后置操作
     * @param $items
     * @return mixed
     */
    protected function _afterIndex($items)
    {
        $result = [];
        foreach ($items as $key => $value)
        {
            if ($value instanceof \EasySwoole\ORM\AbstractModel)
            {
                $value = $value->toArray();
            }
            $result[$key] = $this->getTemplate($value);
        }
        return $result;
    }

    protected function _afterEditGet($data)
    {
        return $data;
    }

    /**
     * 将客户端提交的post合并到origin, 允许新增，少了的字段保持原值
     * @return array
     */
    protected function getSave($post = [], $origin = [], $split = '.')
    {
        foreach ($post as $key => $value)
        {
            $deep = $this->mergeToSave($key, $value, $split);
            $origin = array_merge_multi($origin, $deep);
        }
        return $origin;
    }

    /**
     * 将数据库的结构拍平发送给客户端，即origin格式转化为post格式
     */
    protected function getTemplate($origin = [], $split = '.')
    {
        if (empty($origin))
        {
            return [];
        }
        $template = [];
        foreach ($origin as $key => $value)
        {
            $this->toPostStruct($template, $key, $value, $split);
        }

        return $template;
    }

    private function mergeToSave($key, $value = '', $split = '.')
    {
        if (strpos($key, $split) === false)
        {
            return [$key => $value];
        }

        $result = [];
        list ($first, $last) = explode($split, $key, 2);

        if (strpos($last, $split) !== false)
        {
            $result[$first] = $this->mergeToSave($last, $value);
        }
        else
        {
            return [$first => [ $last => $value]];
        }
        return $result;
    }

    /**
     * 将数据库extension结构转换为post类型
     * 找到每一个叶子节点
     */
    private function toPostStruct(& $sign, $key, $value, $split = '.')
    {
        if (is_array($value))
        {
            foreach ($value as $lk => $lv)
            {
                $fullKey = $key . $split . $lk;
                if (is_array($lv))
                {
                    $this->toPostStruct($sign, $fullKey, $lv, $split);
                } else {
                    $sign[$fullKey] = $lv;
                }
            }
        } else {
            $sign[$key] = $value;
        }
    }
}

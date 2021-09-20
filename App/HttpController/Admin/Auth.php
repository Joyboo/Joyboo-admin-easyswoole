<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use App\Common\Languages\Dictionary;
use App\Model\Admin;
use Linkunyuan\EsUtility\Classes\LamJwt;

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

    protected $sort = ['sort', 'asc'];

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
        // 关联的分组信息
        $relation = $data->relation ? $data->relation->toArray() : [];
        $this->operinfo = $data->toArray();
        $this->operinfo['role'] = $relation;

        // 权限验证
        $this->checkAuth();

        return true;
    }

    protected function checkAuth()
    {
        // todo 权限验证
    }

    // todo 以下方法需要权限限制

    public function add()
    {
        $result = $this->Model->data($this->post)->save();
        $result ? $this->success() : $this->error(Code::ERROR);
    }

    public function edit()
    {
        $post = $this->post;
        $pk = $this->Model->schemaInfo()->getPkFiledName();
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
        $upd = $model->update($this->post);
        if ($upd === false)
        {
            trace('edit update失败: ' . $model->lastQueryResult()->getLastError());
        }
        // 影响行数
        $rowCount = $model->lastQueryResult()->getAffectedRows();
        $rowCount ? $this->success() : $this->error(Code::ERROR, Dictionary::ERROR);
    }

    public function del()
    {
        $get = $this->get;
        $pk = $this->Model->schemaInfo()->getPkFiledName();
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
        $form = $this->post;
        foreach (['id', 'column', 'status'] as $col)
        {
            if (!isset($form[$col]))
            {
                return $this->error(Code::ERROR_5, Dictionary::ADMIN_7);
            }
        }
        $result = $this->Model->update([$form['column'] => $form['status']], ['id' => $form['id']]);
        $result ? $this->success() : $this->error(Code::ERROR);
    }

    public function index()
    {
        $page = $this->get['page'] ?? 1;          // 当前页码
        $limit = $this->get['pageSize'] ?? 20;    // 每页多少条数据

        if ($where = $this->_search())
        {
            $this->Model->where($where);
        }
        if ($this->sort)
        {
            $this->Model->order(...$this->sort);
        }

        $model = $this->Model->limit($limit * ($page - 1), $limit)->withTotalCount();
        $items = $model->all($where);

        $result = $model->lastQueryResult();
        $total = $result->getTotalCount();

        // 后置操作
        $items = $this->_afterIndex($items);
        $this->success(['items' => $items, 'total' => $total]);
    }

    /**
     * 构造查询数据
     * @return array
     */
    protected function _search():array
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
        return $items;
    }
}

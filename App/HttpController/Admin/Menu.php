<?php


namespace App\HttpController\Admin;

use App\Common\Http\Code;
use WonderGame\EsUtility\HttpController\Admin\MenuTrait;

/**
 * Class Menu
 * @property \App\Model\Admin\Menu $Model
 * @package App\HttpController\Admin
 */
class Menu extends Auth
{
    protected array $_authOmit = ['getMenuList', 'treeList'];

    use MenuTrait;

    public function del()
    {
        $this->Model->startTrans();
        try {
            $id = $this->post['id'];
            $opt = $this->post['opt'];

            $model = $this->Model->where('id', $id)->get();
            if ( ! $model) {
                return $this->error(Code::ERROR_OTHER, '删除失败');
            }

            // 删除子元素
            if ($opt === 'del' && ! empty($this->post['chilrenids'])) {
                $chilrenids = explode(',', $this->post['chilrenids']);
                $this->Model->_clone()->destroy($chilrenids);
            }
            // 转移子元素到另一菜单下
            else if ($opt === 'change' && ! empty($this->post['changeid'])) {
                $this->Model->_clone()->update(['pid' => $this->post['changeid']], ['pid' => $id]);
            }

            $model->destroy();
            $this->Model->commit();

            $this->success();

        } catch (\Exception $e) {
            $this->Model->rollback();
            throw $e;
        }
    }
}

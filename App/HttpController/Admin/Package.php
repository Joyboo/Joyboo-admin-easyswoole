<?php


namespace App\HttpController\Admin;


use App\Common\Http\Code;

class Package extends Auth
{
    protected $_uckAction = 'gkey';

    protected $_ckAction = ['saveAdjustEvent' => 'edit'];

    protected function _search()
    {
        if (isset($this->get['gameid']))
        {
            $this->Model->where('gameid', $this->get['gameid']);
        }
        if (isset($this->get['name']))
        {
            $name = "%{$this->get['name']}%";
            $this->Model->where("(name like ? or pkgbnd like ?)", [$name, $name]);
        }
        return false;
    }

    public function gkey()
    {
        $rand = [
            'logkey' => mt_rand(50, 60),
            'paykey' => mt_rand(70, 80)
        ];
        if (!isset($this->get['column']) || !isset($rand[$this->get['column']]))
        {
            return $this->error(Code::ERROR);
        }

        $sign = uniqid($rand[$this->get['column']]);

        $this->success($sign);
    }

    protected function _afterEditGet($data)
    {
        if (is_array($data['extension']['adjust']['event'])) {
            $data['extension']['adjust']['event'] = $this->unformatAdjust($data['extension']['adjust']['event']);
        }
        if (is_string($data['extension']['qzf']['pf'])) {
            $data['extension']['qzf']['pf'] = explode(',', $data['extension']['qzf']['pf']);
        }
        return $data;
    }

    protected function _writeBefore()
    {
        if (is_array($this->post['extension']['qzf']['pf']))
        {
            $this->post['extension']['qzf']['pf'] = implode(',', $this->post['extension']['qzf']['pf']);
        }
        $this->post['extension']['adjust']['event'] = $this->formatAdjust($this->post['extension']['adjust']['event']);
    }

    // 单纯的保存adjust事件
    public function saveAdjustEvent()
    {
        $adjust = $this->formatAdjust($this->post['adjust']);
        $model = $this->Model->where('id', $this->post['id'])->get();
        $extension = $model->getAttr('extension');
        // ['extension']['adjust']['event'] 比较深
        $extension['adjust']['event'] = $adjust;
        $model->extension = $extension;
        $model->update();
        $this->success();
    }

    protected function formatAdjust($event)
    {
        $data = [];
        foreach($event as $ent)
        {
            if (empty($ent['Key']) || empty($ent['Value']))
            {
                continue;
            }
            $data[$ent['Key']] = $ent['Value'];
        }
        return $data;
    }

    protected function unformatAdjust($event)
    {
        $result = [];
        foreach ($event as $key => $value)
        {
            $result[] = [
                'Key' => $key,
                'Value' => $value
            ];
        }
        return $result;
    }
}

<?php
// +----------------------------------------------------------------------
// | CatchAdmin [Just Like ～ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://catchadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://github.com/yanwenwu/catch-admin/blob/master/LICENSE.txt )
// +----------------------------------------------------------------------
// | Author: JaguarJack [ njphper@gmail.com ]
// +----------------------------------------------------------------------

namespace catchAdmin\wechat\repository;

use catchAdmin\wechat\model\WechatTags;
use catchAdmin\wechat\model\WechatUsers;
use catcher\base\CatchRepository;
use catcher\library\WeChat;

class WechatTagsRepository extends CatchRepository
{
    protected $wechatTag;

    public function __construct(WechatTags $tags)
    {
        $this->wechatTag = $tags;
    }

    /**
     * 模型
     *
     * @time 2020年06月21日
     * @return WechatTags
     */
    protected function model()
    {
        return $this->wechatTag;
    }

    /**
     * 获取列表
     *
     * @time 2020年06月21日
     * @param array $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @return mixed
     */
    public function getList($params)
    {
        if (isset($params['all'])) {
            return $this->wechatTag->select();
        }

        return parent::getList(); // TODO: Change the autogenerated stub
    }

    /**
     * 同步微信标签
     *
     * @time 2020年06月21日
     * @return int
     */
    public function sync()
    {
        $tags = $this->wechatTag->column('name');

        $wechatTags = WeChat::officialAccount()->user_tag->list()['tags'];

        $_tags = [];
        foreach ($wechatTags as $key => $tag) {
            if (in_array($tag['name'], $tags)) {
                continue;
            }

            $_tags[] = [
                'tag_id' => $tag['id'],
                'name' => $tag['name'],
                'fans_amount' => $tag['count'],
                'created_at' => time(),
                'updated_at' => time()
            ];
        }

        return $this->wechatTag->insertAll($_tags);
    }

    public function storeBy(array $data)
    {
        $res = WeChat::throw(WeChat::officialAccount()->user_tag->create($data['name']));

        $data['tag_id'] = $res['tag']['id'];

        return $this->wechatTag->storeBy($data);
    }

    /**
     * 更新
     *
     * @time 2020年06月21日
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateBy(int $id, array $data)
    {
        $tag = $this->findBy($id);

        WeChat::throw(WeChat::officialAccount()->user_tag->update($tag->tag_id,$data['name']));

        return parent::updateBy($id, $data); // TODO: Change the autogenerated stub
    }

    /**
     * 删除
     *
     * @time 2020年06月21日
     * @param int $id
     * @return mixed
     */
    public function deleteBy(int $id)
    {
        $tag = $this->findBy($id);

        WeChat::throw(WeChat::officialAccount()->user_tag->delete($tag->tag_id));

        return parent::deleteBy($id); // TODO: Change the autogenerated stub
    }
}
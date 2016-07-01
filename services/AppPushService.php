<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 28/6/2016
 * Time: 6:18 PM
 */

namespace someet\common\services;


use someet\common\models\AppPush;

class AppPushService extends BaseService
{

    /**
     * 设置一条消息为已读
     * @param integer $msg_id 消息编号
     * @param integer $user_id 用户编带
     * @return bool 是否更新成功
     */
    public function readMsg($msg_id, $user_id)
    {
        $efectNum = AppPush::updateAll(['is_read' => AppPush::IS_READ_YES], ['id' => $msg_id, 'user_id' => $user_id, 'is_read' => AppPush::IS_READ_NO]);
        return 1 == $efectNum;
    }

}
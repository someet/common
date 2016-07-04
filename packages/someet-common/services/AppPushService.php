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

    /**
    * 极光推送服务
    */
    public static function jpush($id, $content)
    {
        //发送push
        $res = Yii::$app->jpush->push()
                ->setPlatform(['ios', 'android'])
                // ->addAllAudience()
                ->addRegistrationId([$id])
                    //->addTag(['北京'])
                //->addAlias('alias1')
                        // ->addAndroidNotification('Hi, android notification', 'notification title', 1, ['key1' => 'value1', 'key2' => 'value2'])
                    ->addIosNotification($content, null, "+1", true, "ios category", ['key1' => 'value1'])
                ->setNotificationAlert('test'.date('Y-m-d H:i:s', time()))
                ->send();

        return $res;
    }

}

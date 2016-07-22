<?php
namespace someet\common\services;

use someet\common\models\AppPush;
use Yii;

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
     * 极光推送Yii::error('msg');
     * @param  integer $jpush 极光推送数据
     * @return bool    推送是否成功
     */
    public static function jpush($jpush)
    {
        // 当数据为空时返回值为0
        if (empty($jpush)) {
            return AppPush::QUEUE_SEND_YET;
        }

        //推送
        $res = Yii::$app->jpush->push()
                ->setPlatform(['ios', 'android'])
                // ->addAllAudience()
                ->addRegistrationId([$jpush->jiguang_id])
                //->addTag(['北京'])
                //->addAlias('alias1')
                // ->addAndroidNotification('Hi, android notification', 'notification title', 1, ['key1' => 'value1', 'key2' => 'value2'])
                ->addIosNotification($jpush->content, null, "+1", true, "ios category", [
                        'from_type' => $jpush->from_type,
                        'from_id' => $jpush->from_id,
                        'from_status' => $jpush->from_status,
                        'push_at' => $jpush->push_at,
                ])
                ->send();
        if ($res) {
            return AppPush::QUEUE_SEND_SUCC;
        }
        return AppPush::QUEUE_SEND_YET;
    }
}

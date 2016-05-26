<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 6:04 PM
 */

namespace someet\common\services;

use dektrium\user\models\Account;
use someet\common\models\ActivityCheckIn;
use someet\common\models\Answer;
use someet\common\models\Noti;
use someet\common\models\NotificationTemplate;

class ActivityCheckInService extends BaseService
{


    /**
     * 签到
     *
     * @param int $answer_id 报名ID
     * @param int $seconds 超过多少秒则为迟到,默认是15分钟
     * @return array
     * @throws \yii\db\Exception
     */
    public function checkIn($answer_id, $seconds = 900)
    {
        //查找报名信息
        $answer = Answer::find()
            ->where(['id' => $answer_id])
            ->with(
                [
                    'user',
                    'user.profile',
                    'activity'
                ]
            )
            ->asArray()
            ->one();
        if (!$answer) {
            $this->setError('报名信息不存在');
            return false;
        }
        $activity_id = $answer['activity_id'];

        $user = $answer['user'];
        $user_id = $user['id'];
        $username = $user['username'];

        $activity = $answer['activity'];
        $start_time = $activity['start_time'];

        $exists_check_in = ActivityCheckIn::find()
            ->where(
                [
                    'user_id' => $user_id,
                    'activity_id' => $activity_id
                ]
            )
            ->exists();

        if ($exists_check_in) {
            $this->setError('当前用户已经签到');
            return false;
        }

        $check_in = new ActivityCheckIn();
        $check_in->user_id = $user_id;
        $check_in->username = $username;
        $check_in->status = ActivityCheckIn::STATUS_CHECK_YES;
        $transaction= $check_in->getDb()->beginTransaction();
        if (!$check_in->save()) {
            $transaction->rollBack();
            $this->setError('签到失败,签到信息保存失败');
            return false;
        }

        /*
         * push系统
         * 来源id  微信渠道 TUNNEL_WECHAT 1; 短信渠道 TUNNEL_SMS 2;
         * 来源类型 活动类型 FROM_ACTIVITY 1; 用户类型 FROM_USER 2; * 系统类型 FROM_SYSTEM  3; 场地类型 FROM_SPACE 4;
         * 微信公开id 用于给指定的用户发送消息
         */
        $account = Account::find()
            ->where(['user_id' => $user_id])
            ->one();
        if (!$account) {
            $transaction->rollBack();
            $this->setError('用户没有关联微信');
            return false;
        }
        $openid = $account->client_id;

        $noti = new Noti();
        $noti->tunnel_id = Noti::TUNNEL_WECHAT;
        $noti->user_id = $user_id;
        $noti->from_id_type = Noti::FROM_ACTIVITY;
        $noti->from_id = $activity_id;
        $noti->note = json_encode(NotificationTemplate::fetchSuccessCheckInWechatTemplateData($openid, $user, $activity));

        if (!$noti->save()) {
            $transaction->rollBack();
            $this->setError('签到失败,通知消息保存失败');
            return false;
        }

        //签到时间大于活动开始时间15分钟后算迟到
        $status_arrive = time() > $start_time + $seconds ? Answer::STATUS_ARRIVE_LATE : Answer::STATUS_ARRIVE_ON_TIME;

        $answer = Answer::find()
            ->where(['user_id' => $user_id, 'activity_id' => $activity_id])
            ->one();
        if (!$answer) {
            $transaction->rollBack();
            $this->setError('签到失败,该用户针对当前活动报名信息不存在');
            return false;
        }

        //更新到达状态
        $answer->arrive_status = $status_arrive;
        if (!$answer->save()) {
            $transaction->rollBack();
            $this->setError('签到失败,更新报名信息状态失败');
            return false;
        }

        $transaction->commit();
        return true;
    }
}

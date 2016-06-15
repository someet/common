<?php
namespace app\commands;

use app\components\NotificationTemplate;
use someet\common\models\Noti;
use someet\common\components\SomeetValidator;
use someet\common\models\Activity;
use Yii;
use someet\common\models\Answer;
use dektrium\user\models\Account;
use someet\common\models\User;

class CronController extends \yii\console\Controller
{

    /**
     * 发送通知,包括各渠道，各类型的通知
     */
    public function actionSendNoti()
    {
        //查询所有的未发送的通知
        $noties = Noti::find()
                ->where(["sended_at" => 0])
                ->with(['user'])
                ->asArray()
                ->all();

        foreach ($noties as $noti) {
            //判断渠道为微信
            if (Noti::TUNNEL_WECHAT == $noti['tunnel_id']) {
                // 查询出openid
                

                $from_id = $noti['from_id'];
                $account = Account::find()->where([
                    'provider' => 'wechat',
                    'user_id' => $noti['user']['id'],
                ])->with('user')->one();

                if (!$account) {
                    Yii::error('报名id: ' . $noti['user']['id'] . ' 的用户没有绑定微信');
                    continue;
                }

                //判断来源类型为活动
                if (Noti::FROM_ACTIVITY == $noti['from_id_type']) {
                    $templateData = $noti['note'];
                } elseif (Noti::FROM_USER == $noti['from_id_type']) {
                }

                if (empty($templateData)) {
                    continue;
                }
                // 放入队列
                $wechat_template = Yii::$app->beanstalk->putInTube('wechatofficial', ['templateData' => $templateData, 'noti' => $noti]);
                if (!$wechat_template) {
                    Yii::error('微信消息加到队列失败，请检查');
                } else {
                    Yii::info($noti['user_id'].' 微信模板消息到消息队列成功');
                }

            // 判断渠道为短信
            } elseif (Noti::TUNNEL_SMS == $noti['tunnel_id']) {
            // 判断渠道为app
            } elseif (Noti::TUNNEL_APP == $noti['tunnel_id']) {
            // 判断渠道为站内信
            } elseif (Noti::TUNNEL_MSG == $noti['tunnel_id']) {
            }
        }
    }

    /**
     * 发送审核通知
     * 每天晚上8点执行
     */
    public function actionSendReviewNoti()
    {
        // 给活动开始时间大于当前时间的, 审核的用户发短信, 包括通过的, 拒绝的
        $answerList = Answer::find()
            ->where(['answer.is_send' => Answer::STATUS_SMS_YET, 'activity.status' => Activity::STATUS_RELEASE ])
            ->andWhere(['in', 'answer.status', [Answer::STATUS_REVIEW_PASS, Answer::STATUS_REVIEW_REJECT]])
            ->andWhere('activity.start_time >'.time())
            ->innerJoin('activity', 'answer.activity_id =  activity.id')
            ->with(['user', 'activity', 'activity.pma', 'activity.user'])
            ->asArray()
            ->all();
        
        //遍历列表
        foreach ($answerList as $answer) {
            //判断报名的用户是否存在
            if (!$answer['user']) {
                //记录一个错误, 提示计划任务中报名的用户不存在, 请检查
                Yii::error('计划任务中活动id为'.$answer['activity']['id'].' 的报名的用户不存在, 请检查');
                //继续下一个
                continue;
            }

            // 用户的手机号码不为空, 并且手机号码是合法的手机号
            if (!empty($answer['user']['mobile']) && SomeetValidator::isTelNumber($answer['user']['mobile'])) {
                //手机号
                $mobile = $answer['user']['mobile'];

                //默认获取不通过的短信内容
                $smsData = NotificationTemplate::fetchFailSmsData($answer['activity']['start_time'], $answer['activity']['title']);

                //判断状态是通过
                if (Answer::STATUS_REVIEW_PASS == $answer['status']) {
                    //获取通过的短信内容
                    $smsData = NotificationTemplate::fetchSuccessSmsData($answer['activity']['start_time'], $answer['activity']['title']);
                }

                $mixedData = [
                    'mobile' => $mobile,
                    'smsData' => $smsData,
                    'answer' => $answer
                ];

                $sms = Yii::$app->beanstalk
                    ->putInTube('sms', $mixedData);
                if (!$sms) {
                    Yii::error('短信添加到消息队列失败, 请检查');
                }

                //尝试发送微信模板消息
                //获取绑定的微信对象
                /* @var $account Account */
                // $account = Account::find()->where([
                //     'provider' => 'wechat',
                //     'user_id' => $answer['user']['id'],
                // ])->with('user')->one();

                // //如果短信发送成功绑定了微信对象
                // if ($account) {

                //     //获取微信的openid
                //     $openid = $account->client_id;

                //     //默认获取不通过的模板消息内容
                //     // $templateData = NotificationTemplate::fetchFailedWechatTemplateData($openid, $answer['user'], $answer['activity']);

                //     //如果通过
                //     if (Answer::STATUS_REVIEW_PASS == $answer['status']) {
                //         //获取通过的模板消息内容
                //         $templateData = NotificationTemplate::fetchSuccessWechatTemplateData($openid, $answer['user'], $answer['activity']);
                //         $wechat_template = Yii::$app->beanstalk->putInTube('wechat', ['templateData' => $templateData, 'answer' => $answer]);
                //     }

                //     if (!$wechat_template) {
                //         Yii::error('参加活动提醒微信消息模板加到队列失败，请检查');
                //     } else {
                //         Yii::info('添加微信模板消息到消息队列成功');
                //     }

                // } else {
                //     //记录一个错误, 当前报名用户短信发送失败或者没有绑定微信
                //     Yii::error('报名用户id: '.$answer['user']['id'].' 的用户短信发送失败或者没有绑定微信');
                // }
            } else {
                //报一个错误, 用户手机号码有误, 无法发送短信
                Yii::error('报名用户id: '.$answer['user']['id'].' 的用户手机号码未设置, 或者设置的不正确');
            }
        } // foreach结束
    }

    /**
     * 发送参加活动的提醒在活动前2小时发送
     */
    public function actionSendJoinNoti()
    {
        //先找到在2个小时内即将开始的活动IDs, 并且活动在2个小时内即将开始, 并且当时时间不能大于开始时间
        $activities = Activity::find()
            ->where(['status' => Activity::STATUS_RELEASE])
            ->andWhere(" start_time > " . time() . " and start_time < " . (time()+7200))
            ->asArray()
            ->all();
        if (count($activities)<=0) {
            return;
        }

        $activities_ids = array_column($activities, 'id');
        //查询需要发送提醒的用户, 并且只给审核通过的人发提醒, 并且已经给用户发送过通知短信
        $answerList = Answer::find()
                ->where(['in', 'id', $activities_ids])
                ->andWhere(['answer.join_noti_is_send' => Answer::JOIN_NOTI_IS_SEND_YET, 'answer.status' => Answer::STATUS_REVIEW_PASS, 'answer.is_send' => Answer::STATUS_SMS_SUCC]) //还未给用户发送过参加活动通知
                ->with([
                    'user',
                    'activity'
                ])
                ->asArray()
                ->all();

        //查询今天北京的天气情况
        //"您报名的活动“#activity_title#”在今天的#start_time#开始。当前室外温度1℃，PM25指数95，请合理安排时间出行，不要迟到哦。"
        $weatherArr = Yii::$app->weather->getWeather();
        if (0 == $weatherArr['success']) {
            $weather = "";
        } else {
            $weather = "当前室外温度{$weatherArr['temperature']}℃ ，PM2.5指数{$weatherArr['pm25']}，";
        }

        //遍历列表
        foreach ($answerList as $answer) {
            //判断报名的用户是否存在
            if (!$answer['user']) {
                //记录一个错误, 提示计划任务中报名的用户不存在, 请检查
                Yii::error('参加活动提醒的计划任务中活动id为'.$answer['activity']['id'].' 的报名的用户不存在, 请检查');
                //继续下一个
                continue;
            }

            // 用户的手机号码不为空, 并且手机号码是合法的手机号
            if (!empty($answer['user']['mobile']) && SomeetValidator::isTelNumber($answer['user']['mobile'])) {
                //手机号
                $mobile = $answer['user']['mobile'];

                //设置默认的短信为等待的短信内容
                $smsData = NotificationTemplate::fetchNotiSmsData($answer['activity']['title'], date('H:i', $answer['activity']['start_time']), $weather);

                $mixedData = [
                    'mobile' => $mobile,
                    'smsData' => $smsData,
                    'answer' => $answer,
                ];

                //add noti beanstalk
                $sms = Yii::$app->beanstalk
                    ->putInTube('noti', $mixedData);
                if (!$sms) {
                    Yii::error('参加活动提醒短信添加到消息队列失败, 请检查');
                } else {
                    Yii::info('添加短信到消息队列成功');
                }

                //add noti wechat beanstalk
                //尝试发送微信模板消息
                //获取绑定的微信对象
                /* @var $account Account */
                $account = Account::find()->where([
                    'provider' => 'wechat',
                    'user_id' => $answer['user']['id'],
                ])->with('user')->one();

                //如果短信发送成功绑定了微信对象
                if ($account) {
                    //获取微信的openid
                    $openid = $account->client_id;

                    //设置模板消息默认为等待的模板消息内容
                    $templateData = NotificationTemplate::fetchNotiWechatTemplateData($openid, $answer['activity']);

                    $wechat_template = Yii::$app->beanstalk->putInTube('notiwechat', ['templateData' => $templateData, 'answer' => $answer]);
                    if (!$wechat_template) {
                        Yii::error('参加活动提醒微信消息模板加到队列失败，请检查');
                    } else {
                        Yii::info('添加微信模板消息到消息队列成功');
                    }
                } else {
                    //记录一个错误, 当前报名用户短信发送失败或者没有绑定微信
                    Yii::error('报名用户id: '.$answer['user']['id'].' 的用户短信发送失败或者没有绑定微信');
                }
            } else {
                //报一个错误, 用户手机号码有误, 无法发送短信
                Yii::error('报名用户id: '.$answer['user']['id'].' 的用户手机号码未设置, 或者设置的不正确');
            }
        } // foreach结束
    }

    /**
     * 恢复所有用户允许报名参加活动的次数
     *
     * @param int $times 次数, 默认5次
     */
    public function actionRecoverMemberAllowJoinTimes($times = 5)
    {
        $sql = "UPDATE `user` SET `allow_join_times`='$times'" ;

        $result = Yii::$app->db->createCommand($sql)->execute();

        Yii::info($result ? 'recover member allow join times success' : 'recover member allow join times failed');
    }

    /**
     * 将用户惩罚的分数清零 / 每月
     *
     */
    public function actionCleanPunishScoreMonthly()
    {
        $sql = "UPDATE `user` SET `punish_score`='0' ";

        $result = Yii::$app->db->createCommand($sql)->execute();

        Yii::info($result ? 'clean punish score monthly success' : 'clean punish score monthly failed');
    }

    /**
     * 设置用户的报名状态为审核被拒绝
     */
    public function actionSetUserAnswerAsReviewReject()
    {
        //查找报名的活动现在已经开始,并且用户状态为未审核
        $answers = Answer::find()
            ->join('LEFT JOIN', 'activity', 'activity.id = answer.activity_id')
            ->where('activity.start_time < ' . time())
            ->andWhere(['answer.status' => Answer::STATUS_REVIEW_YET])
            ->asArray()
            ->all();

        //获取报名的id列表
        $answer_ids = array_column($answers, 'id');

        if (is_array($answer_ids) && count($answer_ids)>0) {
            //统一更新是否已发送和发送时间，以及状态为拒绝
            Answer::updateAll(['status' => Answer::STATUS_REVIEW_REJECT], ['in', 'id', $answer_ids]);
        }
    }

    /**
     * 测试发push
     */
    public function actionTest()
    {
        //尝试发送push
        $res = Yii::$app->jpush->push()
                ->setPlatform(['ios', 'android'])
                ->addAllAudience()
                //->addRegistrationId(['171976fa8a80e7ce083'])
                    //->addTag(['北京'])
                //->addAlias('alias1')
                        ->addAndroidNotification('Hi, android notification', 'notification title', 1, ['key1' => 'value1', 'key2' => 'value2'])
                    ->addIosNotification('🍡CandyZ🍉 , Shaye🔆💯✔️ Hi, iOS notification', null, "+1", true, "ios category", ['key1' => 'value1'])
                ->setNotificationAlert('test'.date('Y-m-d H:i:s', time()))
                ->send();
        var_dump($res);

        $res = Yii::$app->jpush->report();
        var_dump($res);
    }

}

<?php
namespace app\commands;

use app\components\NotificationTemplate;
use someet\common\models\Noti;
use someet\common\models\MobileMsg;
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
                ->where(["sended_at" => 0, 'in_tube' => Noti::IN_TUBE_YET])
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
                    Noti::updateAll(['in_tube' => Noti::IN_TUBE_FAIL], ['id' => $noti['id']]);

                    Yii::error('微信消息加到队列失败，请检查');
                } else {
                    Noti::updateAll(['in_tube' => Noti::IN_TUBE_YES], ['id' => $noti['id']]);

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
     * 主要负责短信发送
     * @return 短信是否成功
     */
    public function actionSendMoblie()
    {
        //查询所有的未发送的通知
        $mobileMsg = MobileMsg::find()
                ->where(["is_send" => MobileMsg::STATUS_SMS_YET, 'is_join_queue' => MobileMsg::STATUS_SMS_YET])
                ->asArray()
                ->all();

        foreach ($mobileMsg as $msg) {
            //判断报名的用户是否存在
            if (!$msg['user_id']) {
                //记录一个错误, 提示计划任务中报名的用户不存在, 请检查
                Yii::error('计划任务中活动id为'.$msg['user_id'].' 的报名的用户不存在, 请检查');
                //继续下一个
                continue;
            }
            
            // 用户的手机号码不为空, 并且手机号码是合法的手机号
            if (!empty($msg['mobile_num']) && SomeetValidator::isTelNumber($msg['mobile_num'])) {
                $mixedData = [
                    'mobile' => $msg['mobile_num'],
                    'content' => $msg['content'],
                    'msg' => $msg,
                ];
                $sms = Yii::$app->beanstalk
                    ->putInTube('moblieMsg', $mixedData);
                if (!$sms) {
                    Yii::error('短信添加到消息队列失败, 请检查');
                }else{
                    echo "短信添加到消息队列成功";
                   MobileMsg::updateAll(
                        ['is_join_queue' => MobileMsg::STATUS_SMS_SUCC, 'send_at' => time()],
                        ['id' => $msg['id']]
                    );
                }
            } else {
                //报一个错误, 用户手机号码有误, 无法发送短信
                Yii::error('报名用户id: '.$msg['user_id'].' 的用户手机号码未设置, 或者设置的不正确');
            }
        }
    }


    /**
     * 测试发短信
     */
    public function actionTest()
    {
        $mobile = '18518368050';
        $smsData = "抱歉你报名的6月26日“只言片语——一款猜心的桌游";
        //尝试发送短消息
        $res = Yii::$app->sms->sendSms($mobile, $smsData);
        var_dump($res);
    }
}

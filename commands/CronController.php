<?php
namespace app\commands;

use Yii;
use someet\common\models\Answer;

class CronController  extends \yii\console\Controller
{

    /**
     * 验证是否是手机号码
     *
     * 国际区号-手机号码
     *
     * @param string $number 待验证的号码
     * @return boolean 如果验证失败返回false,验证成功返回true
     */
    public static function isTelNumber($number) {
        return 0 < preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{4,12}$/', $number);
    }

    /**
     * 获取成功的微信模板消息
     * @param $template_id 模板id
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    private function fetchSuccessWechatTemplateData($template_id, $openid, $account, $activity) {
        $start_time = date('Y年m月d日', $activity->start_time);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'].'activity/'.$activity->id,
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "您好, 您已成功报名{$activity->title}",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account->username}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity->title}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$activity->start_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity->address}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "期待您的参与",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }

    /*
     * 获取等待的微信模板消息
     * @param $template_id 模板id
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    private function fetchWaitWechatTemplateData($template_id, $openid, $activity) {
        $start_time = date('Y年m月d日', $activity->start_time);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "您参加的活动现在正在筛选, 请等待",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$activity->title}",
                    "color" =>"#173177"
                ],
                "keyword2" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword3" => [
                    "value" => "{$activity->area}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "请随时关注活动的更新",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }

    /*
     * 获取失败的微信模板消息
     * @param $template_id 模板id
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    private function fetchFailedWechatTemplateData($template_id, $openid, $account, $activity) {
        $start_time = date('Y年m月d日', $activity->start_time);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "您好, 您已报名被拒绝",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account->username}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity->title}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity->area}",
                    "color" => "#173177"
                ],
                "keyword5" => [
                    "value" => "您未通过活动筛选",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "期待您的参与",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }

    /**
     * 获取成功的短信内容
     * @param string $activity_name 活动名称
     * @param string $pma_wechat_id PMA的微信id
     * @return string 短信内容
     */
    private function fetchSuccessSmsData($activity_name, $pma_wechat_id) {
        //获取通过的短信模板
        return "【Someet活动平台】您好，恭喜您报名的“{$activity_name}”活动已通过筛选。具体事宜请您添加工作人员微信（微信号：{$pma_wechat_id}）后会进行说明。添加时请注明活动名称，期待与您共同玩耍，系统短信，请勿回复。";
    }

    /**
     * 获取等待的短信内容
     * @param string $activity_name 活动名称
     * @return string 等待的短信内容
     */
    private function fetchWaitSmsData($activity_name) {
        //获取拒绝的短信模板
        return "【Someet活动平台】您好，您报名的“{$activity_name}”活动发起人正在筛选中，我们将会在24小时内短信给您最终筛选结果，请耐心等待。谢谢您的支持，系统短信，请勿回复。";
    }

    /**
     * 获取失败的短信内容
     * @param string $activity_name 活动名称
     * @return string 失败的短信内容
     */
    private function fetchFailSmsData($activity_name) {
        //获取拒绝的短信模板
        return "【Someet活动平台】Someet用户您好，很抱歉您报名的“{$activity_name}”活动未通过筛选。关于如何提高报名的成功率，这里有几个小tips，1.认真回答筛选问题； 2.尽早报名，每周二周三是活动推送时间，周四周五报名的成功概率会相对降低很多 3.自己发起活动，优质的发起人是有参与活动特权的哦~ 当然，您还可以添加我们的官方客服Someet小海豹（微信号：someetxhb）随时与我们联系。期待下次活动和你相遇。系统短信，请勿回复。";
    }

    /**
     * 发送审核通知
     * 每天晚上8点执行
     */
    public function actionSendReviewNoti()
    {
        // 给审核的用户发短信, 包括通过的, 等待的, 拒绝的
        $answerList = Answer::find()->where(['is_send' => '0'])->with(['user', 'activity', 'activity.principal'])->all();
        foreach($answerList as $answer) {
            if (!$answer->user) {
                Yii::error('报名的用户不存在, 请检查');
                continue;
            }

            // 判断用户存在, 并且用户的手机号码不为空, 并且手机号码是合法的手机号
            if (!empty($answer->user->mobile) && $this->isTelNumber($answer->user->mobile)) {

                //手机号
                $mobile = $answer->user->mobile;

                //等待的短信内容
                $smsData = $this->fetchWaitSmsData($answer->activity->title);
                if ($answer->status = Answer::STATUS_REVIEW_PASS) {

                    // 给一个默认的pma的微信id[此id可能是我们工作人员的微信id]
                    $pma_wechat_id = \DockerEnv::get('DEFAULT_PRINCIPAL');

                    //获取pma的微信id
                    if ($answer->activity->principal && !empty($answer->activity->principal->wechat_id)) {
                        //pma的微信号
                        $pma_wechat_id = $answer->activity->principal->wechat_id;
                    }
                    $smsData = $this->fetchSuccessSmsData($answer->activity->title, $pma_wechat_id);
                } elseif ($answer->status = Answer::STATUS_REVIEW_REJECT) {
                    $smsData = $this->fetchFailSmsData($answer->activity->title);
                }

                //使用云片发送短消息
                if ($smsStatus = Yii::$app->yunpian->sendSms($mobile, $smsData)) {

                    //修改短信发送状态为成功, 以及修改发送时间
                    Answer::updateAll(['is_send' => Answer::STATUS_SMS_SUCC, 'send_at' => time()],
                        ['id' => $answer->id]);

                    //修改短信的标记为true
                    $smsFlag = true;
                } else {
                    //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                    Answer::updateAll(['is_send' => Answer::STATUS_SMS_Fail, 'send_at' => time()],
                        ['id' => $answer->id]);
                }
            } else {
                //报一个错误, 用户手机号码有误, 无法发送短信
                Yii::error('用户手机号码未设置, 或者设置的不正确');
                return ['msg' => '用户手机号码有误, 无法发送短信'];
            }

            $wechat = Yii::$app->wechat;
            //获取绑定的微信对象
            /* @var $account Account */
            $account = Account::find()->where([
                'provider' => 'wechat',
                'user_id' => $answer->user->id,
            ])->with(['user'])->one();
            //如果绑定了微信对象
            if ($account) {
                //获取微信的openid
                $openid = $account->client_id;

                $template_id = Yii::$app->params['sms.wait_template_id'];
                $templateData = $this->fetchWaitWechatTemplateData($template_id, $openid);
                if ($answer->status == Answer::STATUS_REVIEW_PASS) {
                    $templateData = $this->fetchSuccessWechatTemplateData($template_id, $openid, $answer->user, $answer->activity);
                } elseif ($answer->status == Answer::STATUS_REVIEW_REJECT) {
                    $templateData = $this->fetchFailedWechatTemplateData($template_id, $openid, $answer->user, $answer->activity);
                }

                //发送模板消息
                $msgid = $wechat->sendTemplateMessage($templateData);
                if ($msgid) { //模板消息发送成功

                    //记录一下消息模板发送的时间和状态
                    Answer::updateAll(['wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_SUCC, 'wechat_template_push_at' => time()], ['id' => $answer->id]);

                } else {

                    //记录一下消息模板发送的时间和状态, 状态为失败,后面可以单独的重新发送模板消息
                    Answer::updateAll(['wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_Fail, 'wechat_template_push_at' => time()], ['id' => $answer->id]);
                }
            }


        }
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


}

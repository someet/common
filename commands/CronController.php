<?php
namespace app\commands;

use Yii;
use someet\common\models\Answer;
use dektrium\user\models\Account;
use someet\common\models\User;

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
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    private function fetchSuccessWechatTemplateData($openid, $account, $activity) {
        //获取成功的模板消息id
        $template_id = Yii::$app->params['sms.success_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置成功的模板消息id
            Yii::error('请设置成功的模板消息id');
        }
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
                    "value" => "{$start_time}",
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
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    private function fetchWaitWechatTemplateData($openid, $activity) {
        //获取等待的模板消息id
        $template_id = Yii::$app->params['sms.wait_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置等待的模板消息id
            Yii::error('请设置等待的模板消息id');
        }
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
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    private function fetchFailedWechatTemplateData($openid, $account, $activity) {
        //获取失败的模板消息id
        $template_id = Yii::$app->params['sms.failed_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id');
        }
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
        //默认的PMA的微信id
        $default_pma_wechat_id = \DockerEnv::get('DEFAULT_PRINCIPAL');
        //获取微信组件
        $wechat = Yii::$app->wechat;

        // 给审核的用户发短信, 包括通过的, 等待的, 拒绝的
        $answerList = Answer::find()->where(['is_send' => '0'])->with(['user', 'activity', 'activity.pma'])->all();
        //遍历列表
        foreach($answerList as $answer) {

            //判断报名的用户是否存在
            if (!$answer->user) {
                //记录一个错误, 提示计划任务中报名的用户不存在, 请检查
                Yii::error('计划任务中活动id为'.$answer->activity->id.' 的报名的用户不存在, 请检查');
                //继续下一个
                continue;
            }

            // 用户的手机号码不为空, 并且手机号码是合法的手机号
            if (!empty($answer->user->mobile) && $this->isTelNumber($answer->user->mobile)) {

                //手机号
                $mobile = $answer->user->mobile;

                //设置默认的短信为等待的短信内容
                $smsData = $this->fetchWaitSmsData($answer->activity->title);
                //判断状态是通过
                if (Answer::STATUS_REVIEW_PASS == $answer->status ) {

                    // 给一个默认的pma的微信id[此id可能是我们工作人员的微信id]
                    $pma_wechat_id = $default_pma_wechat_id;

                    //获取pma的微信id
                    if ($answer->activity->pma && !empty($answer->activity->pma->wechat_id)) {
                        //设置pma的微信号为当前活动的pma
                        $pma_wechat_id = $answer->activity->pma->wechat_id;
                    } else {
                        //记录一个错误, 提示活动id为多少的活动没有设置pma, 或者对应pma的微信id为空
                        Yii::error("活动id为: {$answer->activity->id} 的活动没有设置pma, 或者对应pma的微信id为空");
                    }
                    //获取通过的短信内容
                    $smsData = $this->fetchSuccessSmsData($answer->activity->title, $pma_wechat_id);
                } elseif (Answer::STATUS_REVIEW_REJECT == $answer->status) {
                    //获取不通过的短信内容
                    $smsData = $this->fetchFailSmsData($answer->activity->title);
                }

                //尝试发送短消息
                $smsRes = Yii::$app->yunpian->sendSms($mobile, $smsData);
                //如果是未审核,则只修改发送时间
                if (Answer::STATUS_REVIEW_YET == $answer->status) {

                    //修改发送时间, 不修改状态, 不然后台没办法再进行筛选了
                    Answer::updateAll(['send_at' => time()],
                        ['id' => $answer->id]);
                } elseif (!$smsRes) {
                    Yii::error('短信发送失败');

                    //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                    Answer::updateAll(['is_send' => Answer::STATUS_SMS_Fail, 'send_at' => time()],
                        ['id' => $answer->id]);
                } else {
                    //修改短信发送状态为成功, 以及修改发送时间
                    Answer::updateAll(['is_send' => Answer::STATUS_SMS_SUCC, 'send_at' => time()],
                        ['id' => $answer->id]);
                }

                //尝试发送微信模板消息
                //获取绑定的微信对象
                /* @var $account Account */
                $account = Account::find()->where([
                    'provider' => 'wechat',
                    'user_id' => $answer->user->id,
                ])->with('user')->one();

                //如果短信发送成功绑定了微信对象
                if ($smsRes && $account) {
                    //获取微信的openid
                    $openid = $account->client_id;

                    //设置模板消息默认为等待的模板消息内容
                    $templateData = $this->fetchWaitWechatTemplateData($openid, $answer->activity);
                    //如果通过
                    if (Answer::STATUS_REVIEW_PASS == $answer->status) {
                        //获取通过的模板消息内容
                        $templateData = $this->fetchSuccessWechatTemplateData($openid, $answer->user, $answer->activity);
                    } elseif (Answer::STATUS_REVIEW_REJECT == $answer->status) {
                        //获取不通过的模板消息内容
                        $templateData = $this->fetchFailedWechatTemplateData($openid, $answer->user, $answer->activity);
                    }

                    //尝试发送模板消息
                    if ($msgid = $wechat->sendTemplateMessage($templateData)) { //模板消息发送成功

                        //更新报名的模板消息的id, 发送的时间和状态
                        Answer::updateAll(['wechat_template_msg_id' => $msgid, 'wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_SUCC, 'wechat_template_push_at' => time()], ['id' => $answer->id]);
                    } else {

                        //更新报名的模板消息发送的时间和状态, 状态为失败,后面可以单独的重新发送模板消息
                        Answer::updateAll(['wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_Fail, 'wechat_template_push_at' => time()], ['id' => $answer->id]);
                    }
                } else {
                    //记录一个错误, 当前报名用户没有绑定微信
                    Yii::error('报名用户id: '.$answer->user->id.' 的用户没有绑定微信');
                }
            } else {
                //报一个错误, 用户手机号码有误, 无法发送短信
                Yii::error('报名用户id: '.$answer->user->id.' 的用户手机号码未设置, 或者设置的不正确');
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
     * 测试发短信
     */
    public function actionTest()
    {
        $answer = Answer::find()->where(['is_send' => '0'])->with(['user', 'activity', 'activity.pma'])->one();
        $smsData = $this->fetchWaitSmsData('', $answer->activity);
        $mobile = '18518368050';
        //尝试发送短消息
        $res = Yii::$app->yunpian->sendSms($mobile, $smsData);
        var_dump($res);

    }


}

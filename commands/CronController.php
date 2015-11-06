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
     * 发送短信通知
     * 每天10点执行
     */
    public function actionSendReviewSms()
    {
        // 给审核通过的用户发短信
        $answerList = Answer::find()->where(['is_send' => '0', 'status' => Answer::STATUS_REVIEW_PASS])->with(['user', 'activity'])->all();
        foreach($answerList as $answer) {
            $mobile = $answer->user->mobile;
            $activityName = $answer->activity->title;
            $wechat_id = 'cookie-song';
            if ($this->isTelNumber($mobile)) {
                $r = Yii::$app->yunpian->sendSms($mobile, "【Someet活动平台】您好，您报名的“#activity_title#”活动发起人正在筛选中，我们将会在24小时内短信给您最终筛选结果，请耐心等待。谢谢您的支持，系统短信，请勿回复。Someet活动平台】您好，恭喜您报名的“{$activityName}”活动已通过筛选。具体事宜请您添加工作人员微信（微信号：{$wechat_id}）后会进行说明。添加时请注明活动名称，期待与您共同玩耍，系统短信，请勿回复。");
                if (!$r) {
                    echo '手机号: '.$mobile.', 审核通过的短信发送失败' ;
                    echo "\r\n";
                } else {
                    Answer::updateAll(['is_send' => '1', 'send_at' => time()], ['id' => $answer->id]);
                    echo '手机号:'.$mobile.', 审核通过的短信发送成功';
                    echo "\r\n";
                }
            }
        }
        exit(0);
    }

    /**
     * 发送短信通知审核未通过
     */
    public function actionSendNotReviewSms()
    {
        // 给审核不通过的用户发短信
        $answerList = Answer::find()->where(['is_send' => '0', 'status' => Answer::STATUS_REVIEW_REJECT])->with(['user', 'activity'])->all();
        foreach($answerList as $answer) {
            $mobile = $answer->user->mobile;
            $activityName = $answer->activity->title;
            if ($this->isTelNumber($mobile)) {
                $r = Yii::$app->yunpian->sendSms($mobile, "【Someet活动平台】Someet用户您好，很抱歉您报名的“{$activityName}”活动未通过筛选。关于如何提高报名的成功率，这里有几个小tips，1.认真回答筛选问题； 2.尽早报名，每周二周三是活动推送时间，周四周五报名的成功概率会相对降低很多 3.自己发起活动，优质的发起人是有参与活动特权的哦~ 当然，您还可以添加我们的官方客服Someet小海豹（微信号：someetxhb）随时与我们联系。期待下次活动和你相遇。系统短信，请勿回复。");
                if (!$r) {
                    echo '手机号: '.$mobile.', 审核通过的短信发送失败' ;
                    echo "\r\n";
                } else {
                    Answer::updateAll(['is_send' => '1', 'send_at' => time()], ['id' => $answer->id]);
                    echo '手机号:'.$mobile.', 审核通过的短信发送成功';
                    echo "\r\n";
                }
            }
        }
        exit(0);
    }

    /**
     * 发送短信通知等待
     */
    public function actionSendWaitSms()
    {
        // 给审核不通过的用户发短信
        $answerList = Answer::find()->where(['is_send' => '0', 'status' => Answer::STATUS_REVIEW_YET])->with(['user', 'activity'])->all();
        foreach($answerList as $answer) {
            $mobile = $answer->user->mobile;
            $activityName = $answer->activity->title;
            if ($this->isTelNumber($mobile)) {
                $r = Yii::$app->yunpian->sendSms($mobile, "【Someet活动平台】您好，您报名的“{$activityName}”活动发起人正在筛选中，我们将会在24小时内短信给您最终筛选结果，请耐心等待。谢谢您的支持，系统短信，请勿回复。");
                if (!$r) {
                    echo '手机号: '.$mobile.', 审核不通过的短信发送失败';
                    echo "\r\n";
                } else {
                    echo '手机号:'.$mobile.', 审核不通过的短信发送成功';
                    echo "\r\n";
                    // 修改答案的短信通知发送状态
                    Answer::updateAll(['is_send' => '1', 'send_at' => time()], ['id' => $answer->id]);
                }
            }
        }
        exit(0);
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

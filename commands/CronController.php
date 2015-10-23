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
        $answerList = Answer::find()->where(['status' => Answer::STATUS_REVIEW_PASS])->with('user')->all();
        foreach($answerList as $answer) {
            $mobile = $answer->user->mobile;
            if ($this->isTelNumber($mobile)) {
                Yii::$app->yunpian->sendSms($mobile, "【Someet活动平台】您好，您报名的“活动名称”活动发起人正在筛选中，我们将会在24时间短信给您最终筛选结果，请耐心等待。谢谢您的支持，系统短信，请勿回复。");
            }
        }


        // 给审核不通过的用户发短信
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

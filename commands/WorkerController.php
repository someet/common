<?php
namespace app\commands;

use common\models\Noti;
use someet\common\models\Answer;
use someet\common\models\User;
use udokmeci\yii2beanstalk\BeanstalkController;
use yii\helpers\Console;
use Yii;

class WorkerController extends BeanstalkController
{
    // Those are the default values you can override

    const DELAY_PIRORITY = "1000"; //Default priority
    const DELAY_TIME = 5; //Default delay time

    // Used for Decaying. When DELAY_MAX reached job is deleted or delayed with
    const DELAY_MAX = 3;

    // sms 发送审核结果  wechat 发送审核结果 noti 发送活动前开始提醒 notiwechat 发送活动前开始提醒 checkinwechat 签到成功的微信通知
    public function listenTubes(){
        return ["sms", "wechat", "noti", "notiwechat", "wechatofficial"];
    }

    /**
     * 发送活动是否通完
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     */
    public function actionSms($job){
        $sentData = $job->getData();
        try {
            $mobile = $sentData->mobile;
            $smsData = $sentData->smsData;
            $answer = $sentData->answer;

            //尝试发送短消息
            $sms = Yii::$app->sms;
            $smsRes = $sms->sendSms($mobile, $smsData);

            //如果是未审核,则只修改发送时间
            if ( $smsRes && in_array($answer->status, [Answer::STATUS_REVIEW_PASS, Answer::STATUS_REVIEW_REJECT])) {
                //修改短信发送状态为成功, 以及修改发送时间
                Answer::updateAll(['is_send' => Answer::STATUS_SMS_SUCC, 'send_at' => time()],
                    ['id' => $answer->id]);
            } elseif ($sms->hasError()) {

                $error = $sms->getError();

                Yii::error('短信发送失败, 请检查'. is_array($error) ? json_encode($error) : $error);

                //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                Answer::updateAll(['send_at' => time()],
                    ['id' => $answer->id]);
            }

            fwrite(STDOUT, Console::ansiFormat("Sms - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

    /**
     * 发送活动是否通完， 或者等待的微信消息模板
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     */
    public function actionWechat($job){
        $sentData = $job->getData();
        try {
            $templateData = (array) $sentData->templateData;
            $answer = $sentData->answer;

            //获取微信组
            $wechat = Yii::$app->wechat;
            $user_id = $answer->user_id;
            $answerNum = Answer::find()
            ->where(['user_id' => $user_id, 'status' => Answer::STATUS_REVIEW_PASS])
            ->count();
            $user = User::find()->where(['id' => $user_id])->one();
            $userJoinCount = $user->attend_count;
            //尝试发送模板消息
            if ($msgid = $wechat->sendTemplateMessage($templateData)) { //模板消息发送成功

                //更新报名的模板消息的id, 发送的时间和状态
                Answer::updateAll(['wechat_template_msg_id' => $msgid, 'wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_SUCC, 'wechat_template_push_at' => time()], ['id' => $answer->id]);

                // 审核成功后 用户通过次数加 1
                if ($userJoinCount < $answerNum ) {
                
                    if($answer->status == Answer::STATUS_REVIEW_PASS){
                        User::updateAllCounters(['attend_count' => 1],['id' => $user_id ]);
                    }else if ($answer->status == Answer::STATUS_REVIEW_REJECT) {
                        User::updateAllCounters(['reject_count' => 1],['id' => $user_id ]);
                    }
                }
            } else {

                //更新报名的模板消息发送的时间和状态, 状态为失败,后面可以单独的重新发送模板消息
                Answer::updateAll(['wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_Fail, 'wechat_template_push_at' => time()], ['id' => $answer->id]);
                // 审核成功后 用户通过次数加 1
                
                if ($userJoinCount < $answerNum ) {
                
                    if($answer->status == Answer::STATUS_REVIEW_PASS){
                        User::updateAllCounters(['attend_count' => 1],['id' => $user_id ]);
                    }else if ($answer->status == Answer::STATUS_REVIEW_REJECT) {
                        User::updateAllCounters(['reject_count' => 1],['id' => $user_id ]);
                    }
                }
            }

            fwrite(STDOUT, Console::ansiFormat("Wechat - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

    /**
     * 活动开始前的提醒通知消息队列
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::DECAY
     */
    public function actionNoti($job) {
        $sentData = $job->getData();
        try {
            $mobile = $sentData->mobile;
            $smsData = $sentData->smsData;
            $answer = $sentData->answer;

            //尝试发送短消息
            $sms = Yii::$app->sms;
            $smsRes = $sms->sendSms($mobile, $smsData);

            if ($smsRes) {
                //修改参加活动通知的短信发送状态为成功, 以及修改发送时间
                Answer::updateAll(['join_noti_is_send' => Answer::JOIN_NOTI_IS_SEND_SUCC, 'join_noti_send_at' => time()],
                    ['id' => $answer->id]);
            } elseif ($sms->hasError()) {

                $error = $sms->getError();
                Yii::error('短信发送失败, 请检查'. is_array($error) ? json_encode($error) : $error);

                //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                Answer::updateAll(['join_noti_send_at' => time()],
                    ['id' => $answer->id]);
            }

            fwrite(STDOUT, Console::ansiFormat("Noti - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;

        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

    /**
     * 发送活动即将开始的微信消息模板
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     */
    public function actionNotiwechat($job){
        $sentData = $job->getData();
        try {
            $templateData = (array) $sentData->templateData ;
            $answer = $sentData->answer;

            //获取微信组
            $wechat = Yii::$app->wechat;
            //尝试发送模板消息
            if ($msgid = $wechat->sendTemplateMessage($templateData)) { //模板消息发送成功

                //更新报名的模板消息的id, 发送的时间和状态
                Answer::updateAll(['join_noti_wechat_template_msg_id' => $msgid, 'join_noti_wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_SUCC, 'join_noti_wechat_template_push_at' => time()], ['id' => $answer->id]);
            } else {

                //更新报名的模板消息发送的时间和状态, 状态为失败,后面可以单独的重新发送模板消息
                Answer::updateAll(['join_noti_wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_Fail, 'join_noti_wechat_template_push_at' => time()], ['id' => $answer->id]);
            }

            fwrite(STDOUT, Console::ansiFormat("NotiWechat - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

    /**
     * 微信服务号通知
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     *
     */
    public function actionWechatofficial($job) {
        $sentData = $job->getData();
        try {
            $templateData =  $sentData->templateData ;
            // var_dump(json_decode($templateData)); 
            // die;
            $noti = $sentData->noti;

            $wechat = Yii::$app->wechat;
            if ($msgid = $wechat->sendTemplateMessage(json_decode($templateData,true))) {
                Noti::updateAll(['callback_id' => $msgid, 'callback_status' => Noti::CALLBACK_STATUS_SUCCESS, 'sended_at' => 0], ['id' => $noti->id]);
            } else {

                Noti::updateAll(['callback_msg' => $msgid, 'callback_status' => Noti::CALLBACK_STATUS_FAILURE, 'sended_at' => 0], ['id' => $noti->id]);
            }

            fwrite(STDOUT, Console::ansiFormat("Wechatofficial - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

    /**
     * 需要用户反馈的微信消息模板
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     *
     */
/*    public function actionFeedbackchat($job){
        $sentData = $job->getData();
        try {
            $templateData = (array) $sentData->templateData ;
            $answer = $sentData->answer;

            //获取微信组
            $wechat = Yii::$app->wechat;
            //尝试发送模板消息
            if ($msgid = $wechat->sendTemplateMessage($templateData)) { //模板消息发送成功

                //更新报名的模板消息的id, 发送的时间和状态
                Answer::updateAll(['join_noti_wechat_template_msg_id' => $msgid, 'join_noti_wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_SUCC, 'join_noti_wechat_template_push_at' => time()], ['id' => $answer->id]);
            } else {

                //更新报名的模板消息发送的时间和状态, 状态为失败,后面可以单独的重新发送模板消息
                Answer::updateAll(['join_noti_wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_Fail, 'join_noti_wechat_template_push_at' => time()], ['id' => $answer->id]);
            }

            fwrite(STDOUT, Console::ansiFormat("Feedbackchat - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }*/

}
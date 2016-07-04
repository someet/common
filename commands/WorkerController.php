<?php
namespace app\commands;

use someet\common\models\Noti;
use someet\common\models\Answer;
use someet\common\models\User;
use someet\common\models\MobileMsg;
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
    public function listenTubes()
    {
        return ["moblieMsg", "wechatofficial", "jpush"];
    }

    /**
     * 极光推送
     * @param  $job 队列数据
     * @return string
     */
    public function actionJpush($job)
    {
        $sentData = $job->getData();
        $jiguang_id = $job->jiguang_id;
        $content = $job->content;
        $app_push = $app_push;

        try {

            // 极光推送
            $jpush = AppPushService::jpush($id, $content);

            echo 'Result=' . json_encode($jpush) . $br;



        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }

    }

    /**
     * 发送手机短信通知
     * @param Pheanstalk\Job $job
     * @return string
     */
    public function actionMoblieMsg($job)
    {
        $sentData = $job->getData();
        try {
            $mobile = $sentData->mobile;
            $smsData = $sentData->content;
            $msg = $sentData->msg;

            //尝试发送短消息
            $sms = Yii::$app->sms;
            $smsRes = $sms->sendSms($mobile, $smsData);

            //如果是未审核,则只修改发送时间
            if ($smsRes) {
                //修改短信发送状态为成功, 以及修改发送时间
                MobileMsg::updateAll(
                    ['is_send' => MobileMsg::QUEUE_SEND_SUCC, 'send_at' => time()],
                    ['id' => $msg->id]
                );
            } elseif ($sms->hasError()) {
                $error = $sms->getError();

                Yii::error('短信发送失败, 请检查'. is_array($error) ? json_encode($error) : $error);

                //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                MobileMsg::updateAll(
                    ['send_at' => time()],
                    ['id' => $msg->id]
                );
            }

            fwrite(STDOUT, Console::ansiFormat("发送".$msg->username."短信成功"."\n", [Console::FG_GREEN]));
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
    public function actionWechatofficial($job)
    {
        $sentData = $job->getData();
        try {
            $templateData =  $sentData->templateData ;
            $noti = $sentData->noti;
            $wechat = Yii::$app->wechat;

            if ($msgid = $wechat->sendTemplateMessage(json_decode($templateData, true))) {
                Noti::updateAll(['callback_id' => $msgid, 'callback_status' => Noti::CALLBACK_STATUS_SUCCESS, 'sended_at' => time()], ['id' => $noti->id]);
            } else {
                Noti::updateAll(['callback_msg' => $msgid, 'callback_status' => Noti::CALLBACK_STATUS_FAILURE, 'sended_at' => time()], ['id' => $noti->id]);
            }

            fwrite(STDOUT, Console::ansiFormat("Wechatofficial - Everything is allright"."\n", [Console::FG_GREEN]));
            return self::DELETE;
        } catch (\Exception $e) {
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            return self::BURY;
        }
    }

}

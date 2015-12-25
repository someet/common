<?php
namespace app\commands;

use someet\common\models\Answer;
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

    public function listenTubes(){
        return ["sms", "noti"];
    }

    /**
     * 活动开始前的提醒通知消息队列
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     *
     */
    public function actionNoti($job) {
        $sentData = $job->getData();
        try {
            // something useful here
            $mobile = $sentData->mobile;
            $smsData = $sentData->smsData;
            $answer = $sentData->answer;

            //尝试发送短消息
            $sms = Yii::$app->yunpian;
            $smsRes = $sms->sendSms($mobile, $smsData);

            if ($smsRes) {

                //修改参加活动通知的短信发送状态为成功, 以及修改发送时间
                Answer::updateAll(['join_noti_is_send' => Answer::JOIN_NOTI_IS_SEND_SUCC, 'join_noti_send_at' => time()],
                    ['id' => $answer->id]);
            } elseif ($sms->hasError()) {

                $error = $sms->getLastError();
                $msg = is_array($error) && isset($error['msg']) ? $error['msg'] : '发送短信失败';

                Yii::error('短信发送失败, 请检查'. is_array($error) ? json_encode($error) : $error);

                //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                Answer::updateAll(['join_noti_send_at' => time()],
                    ['id' => $answer->id]);
            }

            $everthingIsAllRight = true;
            $everthingWillBeAllRight = false;
            $IWantSomethingCustom = false;

            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything is allright"."\n", [Console::FG_GREEN]));
                //Delete the job from beanstalkd
                return self::DELETE;
            }

            if($everthingWillBeAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything will be allright"."\n", [Console::FG_GREEN]));
                //Delay the for later try
                //You may prefer decay to avoid endless loop
                return self::DELAY;
            }

            if($IWantSomethingCustom==true){
                Yii::$app->beanstalk->release($job);
                return self::NO_ACTION;
            }

            fwrite(STDOUT, Console::ansiFormat("- Not everything is allright!!!"."\n", [Console::FG_GREEN]));
            //Decay the job to try DELAY_MAX times.
            return self::DECAY;

            // if you return anything else job is burried.
        } catch (\Exception $e) {
            //If there is anything to do.
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            // you can also bury jobs to examine later
            return self::BURY;
        }
    }

    /**
     * 发送活动是否通完， 或者等待的短信
     * @param Pheanstalk\Job $job
     * @return string  self::BURY
     *                 self::RELEASE
     *                 self::DELAY
     *                 self::DELETE
     *                 self::NO_ACTION
     *                 self::DECAY
     *
     */
    public function actionSms($job){
        $sentData = $job->getData();
        try {
            // something useful here
            $mobile = $sentData->mobile;
            $smsData = $sentData->smsData;
            $answer = $sentData->answer;

            //尝试发送短消息
            $sms = Yii::$app->yunpian;
            $smsRes = $sms->sendSms($mobile, $smsData);

            //如果是未审核,则只修改发送时间
            if (Answer::STATUS_REVIEW_YET == $answer->status) {

                //修改发送时间, 不修改状态, 不然后台没办法再进行筛选了
                Answer::updateAll(['send_at' => time()],
                    ['id' => $answer->id]);
            } elseif ($smsRes) {

                //修改短信发送状态为成功, 以及修改发送时间
                Answer::updateAll(['is_send' => Answer::STATUS_SMS_SUCC, 'send_at' => time()],
                    ['id' => $answer->id]);
            } elseif ($sms->hasError()) {

                $error = $sms->getLastError();
                $msg = is_array($error) && isset($error['msg']) ? $error['msg'] : '发送短信失败';

                Yii::error('短信发送失败, 请检查'. is_array($error) ? json_encode($error) : $error);

                //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                Answer::updateAll(['send_at' => time()],
                    ['id' => $answer->id]);
            }

            $everthingIsAllRight = true;
            $everthingWillBeAllRight = false;
            $IWantSomethingCustom = false;

            if($everthingIsAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything is allright"."\n", [Console::FG_GREEN]));
                //Delete the job from beanstalkd
                return self::DELETE;
            }

            if($everthingWillBeAllRight == true){
                fwrite(STDOUT, Console::ansiFormat("- Everything will be allright"."\n", [Console::FG_GREEN]));
                //Delay the for later try
                //You may prefer decay to avoid endless loop
                return self::DELAY;
            }

            if($IWantSomethingCustom==true){
                Yii::$app->beanstalk->release($job);
                return self::NO_ACTION;
            }

            fwrite(STDOUT, Console::ansiFormat("- Not everything is allright!!!"."\n", [Console::FG_GREEN]));
            //Decay the job to try DELAY_MAX times.
            return self::DECAY;

            // if you return anything else job is burried.
        } catch (\Exception $e) {
            //If there is anything to do.
            fwrite(STDERR, Console::ansiFormat($e."\n", [Console::FG_RED]));
            // you can also bury jobs to examine later
            return self::BURY;
        }
    }
}
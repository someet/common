<?php
namespace app\commands;

use someet\common\components\SomeetValidator;
use someet\common\models\Activity;
use Yii;
use someet\common\models\Answer;
use dektrium\user\models\Account;
use someet\common\models\User;

class CronController  extends \yii\console\Controller
{

    private $week = [
      0 => '周天',
        1 => '周一',
        2 => '周二',
        3 => '周三',
        4 => '周四',
        5 => '周五',
        6 => '周六',
    ];

    /**
     * 获取成功的微信模板消息
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchSuccessWechatTemplateData($openid, $account, $activity) {
        //获取成功的模板消息id
        $template_id = Yii::$app->params['sms.success_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置成功的模板消息id
            Yii::error('请设置成功的模板消息id');
        }
        $start_time = date('m月d日', $activity['start_time'])
            . $this->week[date('w', $activity['start_time'])]
            . date('H:i', $activity['start_time'])
            . '开始';
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'].'activity/'.$activity['id'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "恭喜，你报名的活动已通过筛选！",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account['username']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity['address']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "点击查看详情，并扫码进入活动群。",
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
        $start_time = date('m月d日', $activity['start_time'])
            . $this->week[date('w', $activity['start_time'])]
            . date('H:i', $activity['start_time'])
            . '开始';
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "您报名的活动正在筛选，请耐心等待",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword2" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword3" => [
                    "value" => "{$activity['area']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "请随时关注Someet服务号的通知，及时收到筛选结果信息。",
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
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "抱歉，你报名的活动未通过筛选",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account['username']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "",
                    "color" => "#173177"
                ],
                "keyword5" => [
                    "value" => "发起人未通过你的报名申请。",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "每个人都有被拒绝的时候，点击详情，试试更多其他活动吧！",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }
    /*
     * 获取参加活动通知的微信模板消息
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    private function fetchNotiWechatTemplateData($openid, $activity) {
        //获取失败的模板消息id
        $template_id = Yii::$app->params['sms.noti_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id');
        }
        $start_time = date('Y年m月d日', $activity['start_time']);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "您好，您预定的活动马上开始！",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$activity['title']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['address']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "请合理安排时间出行，不要迟到哦。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }

    /**
     * 获取成功的短信内容
     * @param string $activity_name 活动名称
     * @return string 短信内容
     */
    private function fetchSuccessSmsData($activity_name) {
        //获取通过的短信模板
        return "恭喜，你报名的“{$activity_name}”活动已通过筛选。活动地点等详细信息将在活动微信群中和大家沟通。请您按以下操作步骤加入活动微信群：进入Someet活动平台（服务号ID：SomeetInc）——点击屏幕下栏“我”——进入相应活动页面——点击微信群组——扫描二维码加入活动群。期待与您共同玩耍，系统短信，请勿回复。";
    }
    /**
     * 获取等待的短信内容
     * @param string $activity_name 活动名称
     * @return string 等待的短信内容
     */
    private function fetchWaitSmsData($activity_name) {
        //获取拒绝的短信模板
        return "你好，你报名的“{$activity_name}”活动，发起人正在筛选中，我们将会在24小时内短信给您最终筛选结果，请耐心等待。谢谢您的支持，系统短信，请勿回复。";
    }
    /**
     * 获取失败的短信内容
     * @param string $activity_name 活动名称
     * @return string 失败的短信内容
     */
    private function fetchFailSmsData($activity_name) {
        //获取拒绝的短信模板
        return "Shit happens!很抱歉你报名的“ {$activity_name}”活动未通过筛选。你可添加官方客服Someet小海豹（微信ID：someetxhb）随时与我们联系。期待下次活动和你相遇。系统短信，请勿回复。";
    }

    /**
     * 获取通知参加活动的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    private function fetchNotiSmsData($activity_name, $start_time, $weather) {
        //获取通知参加活动的短信
        return "你报名的活动“{$activity_name}”在今天的{$start_time}开始。{$weather}请合理安排时间出行，不要迟到哦。";
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

        // 给活动开始时间大于当前时间的, 审核的用户发短信, 包括通过的, 等待的, 拒绝的
        $answerList = Answer::find()
            ->where(['answer.is_send' => Answer::STATUS_SMS_YET])
            ->innerJoin('activity', "activity.start_time > ".time()." and activity.status = ".Activity::STATUS_RELEASE)
            ->with(['user', 'activity', 'activity.pma', 'activity.user'])
            ->asArray()
            ->all();

        //遍历列表
        foreach($answerList as $answer) {
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

                //设置默认的短信为等待的短信内容
                $smsData = $this->fetchWaitSmsData($answer['activity']['title']);
                //判断状态是通过
                if (Answer::STATUS_REVIEW_PASS == $answer['status']) {

                    // 给一个默认的pma的微信id[此id可能是我们工作人员的微信id]
                    $pma_wechat_id = $default_pma_wechat_id;

                    //获取pma的微信id
                    if ($answer['activity']['pma'] && !empty($answer['activity']['pma']['wechat_id'])) {
                        //设置pma的微信号为当前活动的pma
                        $pma_wechat_id = $answer['activity']['pma']['wechat_id'];
                    } else {
                        //记录一个错误, 提示活动id为多少的活动没有设置pma, 或者对应pma的微信id为空
                        Yii::error("活动id为: {$answer['activity']['id']} 的活动没有设置pma, 或者对应pma的微信id为空");
                    }
                    //获取通过的短信内容
                    $smsData = $this->fetchSuccessSmsData($answer['activity']['title']);
                } elseif (Answer::STATUS_REVIEW_REJECT == $answer['status']) {
                    //获取不通过的短信内容
                    $smsData = $this->fetchFailSmsData($answer['activity']['title']);
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
                $account = Account::find()->where([
                    'provider' => 'wechat',
                    'user_id' => $answer['user']['id'],
                ])->with('user')->one();

                //如果短信发送成功绑定了微信对象
                if ($account) {

                    //获取微信的openid
                    $openid = $account->client_id;

                    //设置模板消息默认为等待的模板消息内容
                    $templateData = $this->fetchWaitWechatTemplateData($openid, $answer['activity']);
                    //如果通过
                    if (Answer::STATUS_REVIEW_PASS == $answer['status']) {
                        //获取通过的模板消息内容
                        $templateData = $this->fetchSuccessWechatTemplateData($openid, $answer['user'], $answer['activity']);
                    } elseif (Answer::STATUS_REVIEW_REJECT == $answer['status']) {
                        //获取不通过的模板消息内容
                        $templateData = $this->fetchFailedWechatTemplateData($openid, $answer['user'], $answer['activity']);
                    }

                    $wechat_template = Yii::$app->beanstalk->putInTube('wechat', ['templateData' => $templateData, 'answer' => $answer]);
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
     * 发送参加活动的提醒在活动前2小时发送
     */
    public function actionSendJoinNoti()
    {
        //先找到在2个小时内即将开始的活动IDs, 并且活动在2个小时内即将开始, 并且当时时间不能大于开始时间
        $activities = Activity::find()
            ->where(['status' => Activity::STATUS_RELEASE])
            ->andWhere(" start_time > " . time() . " and start_time < " . (time()+7200) )
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
        foreach($answerList as $answer) {

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
                $smsData = $this->fetchNotiSmsData($answer['activity']['title'], date('H:i', $answer['activity']['start_time']), $weather);

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
                    $templateData = $this->fetchNotiWechatTemplateData($openid, $answer['activity']);

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
     * 测试发短信
     */
    public function actionTest()
    {
        $mobile = '18518368050';
        $smsData = "test in cron/test";
        //尝试发送短消息
        $res = Yii::$app->sms->sendSms($mobile, $smsData);
        var_dump($res);
    }
}

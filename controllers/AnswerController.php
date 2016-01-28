<?php

namespace app\controllers;


use app\components\DataValidationFailedException;
use dektrium\user\models\Account;
use someet\common\models\ActivityFeedback;
use someet\common\models\AdminLog;
use someet\common\models\Answer;
use someet\common\models\Activity;
use someet\common\models\User;
use someet\common\models\AnswerItem;
use someet\common\models\Question;
use someet\common\components\SomeetValidator;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 报名控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class AnswerController extends BackendController
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
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['post'],
                    'view' => ['get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
                'allowActions' => [
                    'create',
                    'view-by-activity-id',
                    'send-notification',
                    'filter',//审核
                    'arrive',//到场情况
                    'leave'//请假
                ]
            ],
        ];
    }

    /**
     * 添加一个答案
     * @return array|null|\yii\db\ActiveRecord
     * @throws DataValidationFailedException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        $data = ['question_id' => $post['question_id'], 'activity_id' => $post['activity_id']];
        $answerList = $post['answerItemList'];

        $model = new Answer();
        if ($model->load($data, '') && $model->save()) {
            if (!empty($answerList)) {
                foreach ($answerList as $answer) {
                    $answerModel = new AnswerItem();
                    if ($answerModel->load($answer, '') && $answerModel->save()) {

                    } elseif ($answerModel->hasErrors()) {
                        $errors = $model->getFirstErrors();
                        throw new DataValidationFailedException(array_pop($errors));
                    } else {
                        throw new ServerErrorHttpException();
                    }
                }
            }

            return Answer::find()
                ->where(['id' => $model->id])
                ->asArray()
                ->with('answerItemList')
                ->one();
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    public static function isTelNumber($number) {
        return 0 < preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{4,12}$/', $number);
    }

    /**
     * 更新答案的状态
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id)
    {

    }
    /**
     * 更新用户请假
     * @param $id 更新的对象id
     * @param $leave_status 请假状态 0 未请假 1 已请假
     * @return array|null|\yii\db\ActiveRecord
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionLeave($id, $leave_status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 参数验证
        if ($id < 1 || !in_array($leave_status, [0,1])) {
            return ['msg' => '参数不正确'];
        }

        //后台操作日志记录
        AdminLog::saveLog('更新用户请假状态', $id);

        //获取报名信息
        $model = Answer::find()->where(['id' => $id])->with(['user', 'activity'])->one();
        //修改当前报名的状态为通过或者不通过

        //设置答案的状态为通过或不通过
        $model->leave_status = $leave_status;
        if (!$model->save()) {
            //返回错误信息
            return ['msg' => '操作失败'];
        } else {
            //返回正确的消息
            return Answer::find()
                ->where(['id' => $model->id])
                ->asArray()
                ->with('answerItemList')
                ->one();
        }
    }

    /**
     * 更新用户到场情况
     * @param $id 更新的对象id
     * @param $arrive_status 0 未到场  1迟到 2准时
     * @return array|null|\yii\db\ActiveRecord
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionArrive($id, $arrive_status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 参数验证
        if ($id < 1 || !in_array($arrive_status, [0,1, 2])) {
            return ['msg' => '参数不正确'];
        }

        //后台操作日志记录
        AdminLog::saveLog('更新用户到场情况', $id);

        //获取报名信息
        $model = Answer::find()->where(['id' => $id])->with(['user', 'activity'])->one();
        //修改当前报名的状态为通过或者不通过

        //设置答案的状态为通过或不通过
        $model->arrive_status = $arrive_status;
        if (!$model->save()) {
            //返回错误信息
            return ['msg' => '操作失败'];
        } else {
            //返回正确的消息
            return Answer::find()
                ->where(['id' => $model->id])
                ->asArray()
                ->with('answerItemList')
                ->one();
        }
    }

    /**
     * 过滤报名是否通过
     * @param $id 过滤的对象id
     * @param $pass_or_not 通过或者不通过, 值为1或0
     * @return array|null|\yii\db\ActiveRecord
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionFilter($id, $pass_or_not)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 参数验证
        if ($id < 1 || !in_array($pass_or_not, [0,1])) {
            return ['msg' => '参数不正确'];
        }

        //后台操作日志记录
        AdminLog::saveLog('筛选报名结果', $id);

        //获取报名信息
        $model = Answer::find()->where(['id' => $id])->with(['user', 'activity'])->one();
        //修改当前报名的状态为通过或者不通过

        //设置答案的状态为通过或不通过
        $model->status = $pass_or_not ? Answer::STATUS_REVIEW_PASS : Answer::STATUS_REVIEW_REJECT;
        if (!$model->save()) {
            //返回错误信息
            return ['msg' => '审核失败'];
        } else {
            //返回正确的消息
            return Answer::find()
                ->where(['id' => $model->id])
                ->asArray()
                ->with('answerItemList')
                ->one();
        }
    }

    /**
     * 查看一个报名的情况
     * @param integer $id 表单ID
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Question::find()
            ->where(['id' => $id])
            ->asArray()
            ->with('questionItemList')
            ->one();

        return $model;
    }

    // 查看一个活动下面的问题答案
    /**
     * 根据活动id查看报名列表
     * @param integer $activity_id 活动ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionViewByActivityId($activity_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $models = Answer::find()
            ->where(['activity_id' => $activity_id])
            ->with(
                [
                    'answerItemList' => function ($q) {
                    },
                    'user',
                    'user.profile',
                    'activity',
                ]
            )
            ->asArray()
            ->all();

        $feedbacks = ActivityFeedback::find()
            ->where(['activity_id' => $activity_id])
            ->asArray()
            ->all();

        // 遍历反馈
        foreach($feedbacks as $feedback) {
            // 将同一个用户的反馈放到报名对象上面
            foreach($models as &$model) {
                if ($model['user_id'] == $feedback['user_id']) {
                    $model['feedback'] = $feedback;
                }
            }
            //$answer['feedback'] = $feedbacks;
        }

        return $models;
    }

    /**
     * 查找报名
     * @param  integer $id 报名id
     * @return Answer 报名对象
     * @throws NotFoundHttpException 如果找不到则抛出404异常
     */
    public function findModel($id)
    {
        $model = Answer::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("答案不存在");
        }
    }


    /**
     * 获取成功的短信内容
     * @param string $activity_name 活动名称
     * @return string 短信内容
     */
    private function fetchSuccessSmsData($activity_name) {
        //获取通过的短信模板
        return "恭喜，你报名的“{$activity_name}”活动已通过筛选。活动地点等详细信息将在活动微信群中和大家沟通。请你按以下操作步骤加入活动微信群：进入Someet活动平台（服务号ID：SomeetInc）——点击屏幕下栏“我”——进入相应活动页面——点击微信群组——扫描二维码加入活动群。期待与你共同玩耍，系统短信，请勿回复。";
    }
    /**
     * 获取等待的短信内容
     * @param string $activity_name 活动名称
     * @return string 等待的短信内容
     */
    private function fetchWaitSmsData($activity_name) {
        //获取拒绝的短信模板
        return "你好，你报名的“{$activity_name}”活动，发起人正在筛选中，我们将会在24小时内短信给你最终筛选结果，请耐心等待。谢谢你的支持，系统短信，请勿回复。";
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
     * 获取需要反馈的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    private function fetchNeedFeedbackSmsData($activity_name) {
        //获取需要反馈的短信内容
       return " 你好，你已成功参加Someet{$activity_name}的活动，请及时对活动进行反馈，之后会提高下次通过筛选概率哦。";
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
                    "value" => "你报名的活动正在筛选，请耐心等待",
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
                    "value" => "你好，你预定的活动马上开始！",
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

    /*
     * 获取需反馈活动通知的微信模板消息
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    private function fetchNeedFeedbackWechatTemplateData($openid, $account, $activity) {

        //获取需反馈活动通知的微信模板消息id
        $template_id = Yii::$app->params['sms.feedback_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id111');
        }
        $start_time = date('Y年m月d日', $activity['start_time']);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "你好，你已成功参加Someet活动，请及时对活动进行反馈。",
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
                    "value" => "{$activity['area']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "反馈后会提高下次通过筛选概率哦。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
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

    /**
     * 发送通知
     * @param $user_id int 用户编号
     * @param $activity_id int 活动编号
     * @return boolean true(发送成功) | false (发送失败)
     */ 
    public function actionSendNotification($user_id, $activity_id)
     {
        Yii::$app->response->format = Response::FORMAT_JSON;

        //参数校验
        if (empty($user_id) || empty($activity_id)) {
            $result = '活动或者用户不存在';
            return false;
        }

        // 给活动开始时间大于当前时间的, 审核的用户发短信, 包括通过的, 等待的, 拒绝的
        $answer = Answer::find()
            ->where(['answer.is_send' => Answer::STATUS_SMS_YET])
            ->with(['user', 'activity'])
            ->where([ 'activity_id' => $activity_id, 'user_id' => $user_id])
            ->asArray()
            ->one();


        // 用户的手机号码不为空, 并且手机号码是合法的手机号
        if (!empty($answer['user']['mobile']) && SomeetValidator::isTelNumber($answer['user']['mobile'])) {

            //手机号
            $mobile = $answer['user']['mobile'];

            //设置默认的短信为等待的短信内容
            $smsData = $this->fetchWaitSmsData($answer['activity']['title']);
            //判断状态是通过
            if (Answer::STATUS_REVIEW_PASS == $answer['status']) {

                    //获取通过的短信内容
                    $smsData = $this->fetchSuccessSmsData($answer['activity']['title']);

                    if ( Answer::STATUS_ARRIVE_YET != $answer['arrive_status'] && $answer['is_feedback'] == Answer::FEEDBACK_NO ) {
                        //获取需要反馈的短信内容
                        $smsData = $this->fetchNeedFeedbackSmsData($answer['activity']['title']);  
                    }
                } elseif (Answer::STATUS_REVIEW_REJECT == $answer['status']) {
                    //获取不通过的短信内容
                    $smsData = $this->fetchFailSmsData($answer['activity']['title']);
                }

                $mixedData = [
                    'mobile' => $mobile,
                    'smsData' => $smsData,
                    'answer' => $answer
                ];

                $sms = Yii::$app->beanstalk->putInTube('sms', $mixedData);

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
                        if ( Answer::STATUS_ARRIVE_YET != $answer['arrive_status'] && $answer['is_feedback'] == Answer::FEEDBACK_NO ) {
                            //获取需要反馈的短信内容
                            $templateData = $this->fetchNeedFeedbackWechatTemplateData($openid, $answer['user'], $answer['activity']);  
                            $wechatResult = $templateData['data']['first']['value'];
                            // print_r($templateData);
                            }
                    } elseif (Answer::STATUS_REVIEW_REJECT == $answer['status']) {
                        //获取不通过的模板消息内容
                        $templateData = $this->fetchFailedWechatTemplateData($openid, $answer['user'], $answer['activity']);
                        $wechatResult = $templateData['data']['first']['value'];
                    }

                    $wechat_template = Yii::$app->beanstalk->putInTube('wechat', ['templateData' => $templateData, 'answer' => $answer]);
                    if (!$wechat_template) {
                        Yii::error('参加活动提醒微信消息模板加到队列失败，请检查');
                    } else {
                        Yii::info('添加微信模板消息到消息队列成功');
                    }
                    $wechatResult = $templateData['data']['first']['value'];
                } else {
                    //记录一个错误, 当前报名用户短信发送失败或者没有绑定微信
                    Yii::error('报名用户id: '.$answer['user']['id'].' 的用户短信发送失败或者没有绑定微信');
                }
            } else {
                //报一个错误, 用户手机号码有误, 无法发送短信
                Yii::error('报名用户id: '.$answer['user']['id'].' 的用户手机号码未设置, 或者设置的不正确');
            }
        
        return $result = ['status' => '0','sms' => $smsData,'wechatResult' => $wechatResult];

    }

    /**
    * 发送通知
    * @param 
    *
    *
    */
    public function actionSendMessage($user_id){
        $id =$user_id;
        $model = User::findOne($user_id);

                // 用户的手机号码不为空, 并且手机号码是合法的手机号
        $mobile = $model->mobile;

        if (!empty($answer['user']['mobile']) && SomeetValidator::isTelNumber($answer['user']['mobile'])) {
            
        
        // $model = User::find()
        //         ->where(['id' => $user_id])
        //         ->one();
        // $mobile = '18032067618';
        // print_r($model);
        // print_r($mobile);

        $mixedData = [
            'mobile' => $mobile,
            'smsData' => $smsData,
            'answer' => $answer
        ];

        // $sms = Yii::$app->beanstalk
        //     ->putInTube('sms', $mixedData);
        // if (!$sms) {
        //     Yii::error('短信添加到消息队列失败, 请检查');
        // }
        // $smsData = "test in cron/test";
        //尝试发送短消息
            $res = Yii::$app->sms->sendSms($mobile, $smsData);
        }
        // var_dump($res);
    }

}

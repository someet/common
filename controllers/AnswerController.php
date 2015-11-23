<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use dektrium\user\models\Account;
use someet\common\models\AdminLog;
use someet\common\models\Answer;
use someet\common\models\AnswerItem;
use someet\common\models\Question;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class AnswerController extends BackendController
{

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
                    'filter',
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

        //标记发送模板消息的状态
        $templateFlag = false;

        //标记发送短信的状态
        $smsFlag = false;

        //获取报名信息
        $model = Answer::find()->where(['id' => $id])->with(['user', 'activity'])->one();

        //开启事务
        $transaction = $model->getDb()->beginTransaction();

        //获取绑定的微信对象
        /* @var $account Account */
        $account = Account::find()->where([
            'provider' => 'wechat',
            'user_id' => $model->user_id,
        ])->with(['user'])->one();

        //检查是否可以发送微信模板消息
        if ($account) {
            //获取微信的openid
            $openid = $account->client_id;

            //根据状态获取消息模板
            if ($pass_or_not) {//通过

                //获取模板id
                if( empty(Yii::$app->params['sms.success_template_id']) ) {
                    Yii::error('模板消息成功通知的模板id未设置');
                    return ['msg' => '模板消息成功通知的模板id未设置'];
                }
                $template_id = Yii::$app->params['sms.success_template_id'];

                //获取通过的消息模板并填充内容, 设置颜色
                $template_data = $this->fetchSuccessWechatTemplateData($template_id, $openid, $account, $account->activity);
            } else {

                //获取模板id
                if( empty(Yii::$app->params['sms.failed_template_id']) ) {
                    Yii::error('模板消息失败通知的模板id未设置');
                    return ['msg' => '模板消息失败通知的模板id未设置'];
                }

                $template_id = Yii::$app->params['sms.failed_template_id'];
                //获取通过的消息模板并填充内容, 设置颜色
                $template_data = $this->fetchFailedWechatTemplateData($template_id, $openid, $account, $account->activity);
            }

            //获取微信组件
            $wechat = Yii::$app->wechat;
            //发送模板消息
            $msgid = $wechat->sendTemplateMessage($template_data);
            if ($msgid) { //模板消息发送成功

                //记录一下消息模板发送的时间和状态
                Answer::updateAll(['wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_SUCC, 'wechat_template_push_at' => time()], ['id' => $model->id]);

                //修改模板消息的标记为true
                $templateFlag = true;
            } else {

                //记录一下消息模板发送的时间和状态, 状态为失败,后面可以单独的重新发送模板消息
                Answer::updateAll(['wechat_template_is_send' => Answer::STATUS_WECHAT_TEMPLATE_Fail, 'wechat_template_push_at' => time()], ['id' => $model->id]);
            }
        }

        //答案的完整报名对象
        $answerModel = Answer::find()->where(['id' => $id])->with(['user', 'activity', 'activity.principal'])->asArray()->one();

        // 判断用户存在, 并且用户的手机号码不为空, 并且手机号码是合法的手机号
        if ($answerModel['user'] && !is_empty($answerModel['user']['mobile']) && $this->isTelNumber($answerModel['user']['mobile'])) {

            //手机号
            $mobile = $model['user']['mobile'];

            //根据状态获取短信模板
            if ($pass_or_not) {

                //获取pma的微信id
                if ( $answerModel['activity']['principal'] ) {
                    //pma的微信号
                    $pmaWechatId = $answerModel['activity']['principal']['wechat_id'];
                } else {
                    // 给一个默认的pma的微信id[此id可能是我们工作人员的微信id]
                    $pmaWechatId = \DockerEnv::get('DEFAULT_PRINCIPAL');
                }

                //获取通过的短信模板
                $template = "【Someet活动平台】您好，恭喜您报名的“{$answerModel['activity']['title']}”活动已通过筛选。具体事宜请您添加工作人员微信（微信号：{$pmaWechatId}）后会进行说明。添加时请注明活动名称，期待与您共同玩耍，系统短信，请勿回复。";
            } else {
                //获取拒绝的短信模板
                $template = "【Someet活动平台】Someet用户您好，很抱歉您报名的“{$answerModel['activity']['title']}”活动未通过筛选。关于如何提高报名的成功率，这里有几个小tips，1.认真回答筛选问题； 2.尽早报名，每周二周三是活动推送时间，周四周五报名的成功概率会相对降低很多 3.自己发起活动，优质的发起人是有参与活动特权的哦~ 当然，您还可以添加我们的官方客服Someet小海豹（微信号：someetxhb）随时与我们联系。期待下次活动和你相遇。系统短信，请勿回复。";
            }

            //使用云片发送短消息
            if ($smsStatus = Yii::$app->yunpian->sendSms($mobile, $template)) {

                //修改短信发送状态为成功, 以及修改发送时间
                Answer::updateAll(['is_send' => Answer::STATUS_SMS_SUCC, 'send_at' => time()], ['id' => $model->id]);

                //修改短信的标记为true
                $smsFlag = true;
            } else {
                //修改短信发送状态为失败, 以及修改发送时间[方便以后单独发送短信]
                Answer::updateAll(['is_send' => Answer::STATUS_SMS_Fail, 'send_at' => time()], ['id' => $model->id]);
            }
        } else {
            //报一个错误, 用户手机号码有误, 无法发送短信
            Yii::error('用户手机号码未设置, 或者设置的不正确');
            return ['msg' => '用户手机号码有误, 无法发送短信'];
        }

        if ($templateFlag || $smsFlag) {
            //修改当前报名的状态为通过或者不通过

            //设置答案的状态为通过或不通过
            $model->status = $pass_or_not ? Answer::STATUS_REVIEW_PASS : Answer::STATUS_REVIEW_REJECT;
            if (!$model->save()) {
                //回滚事务
                $transaction->rollBack();
                //返回错误信息
                return ['msg' => '审核失败'];
            } else {
                //提交事务
                $transaction->commit();
                //返回正确的消息
                return Answer::find()
                    ->where(['id' => $model->id])
                    ->asArray()
                    ->with('answerItemList')
                    ->one();

            }
        } else {
            //回滚事务
            $transaction->rollBack();
            //返回错误信息
            return ['msg' => '消息模板未发送成功'];
        }
    }

    public function actionDelete($id)
    {
    }

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
    public function actionViewByActivityId($activity_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $models = Answer::find()
            ->where(['activity_id' => $activity_id])
            ->asArray()
            ->with(
                [
                    'answerItemList' => function ($q) {
                    },
                    'user',
                    'user.profile',
                ]
            )
            ->all();

        /*
        // 遍历问题
        foreach($models as &$answer) {
            // 将同一个用户的回答合并成一条记录
            $answerItemList = [];
            foreach($answer['answerItemList'] as $answerItem) {
                $answerItemList[] = $answerItem['question_value'];
            }
            $answer['answerItemList'] = $answerItemList;
        }
        */

        return $models;
    }

    /**
     * @param $id
     * @return Answer
     * @throws NotFoundHttpException
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
}

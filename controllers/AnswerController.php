<?php

namespace app\controllers;


use app\components\DataValidationFailedException;
use dektrium\user\models\Account;
use someet\common\models\ActivityFeedback;
use someet\common\models\AdminLog;
use someet\common\models\Answer;
use someet\common\models\User;
use someet\common\models\AnswerItem;
use someet\common\models\Question;
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
                    'send-message',
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

<?php

namespace app\controllers;
use app\components\NotificationTemplate;
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
                'allowActions' => ['apply',]
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
     * 更新用户取消报名情况
     * @param $id 更新的对象id
     * @param $leave_status 请假状态 0未取消报名  1 取消报名
     * @return array|null|\yii\db\ActiveRecord
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionApply($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 参数验证
        if ($id < 1 || !in_array($status, [0,1])) {
            return ['msg' => '参数不正确'];
        }

        //后台操作日志记录
        AdminLog::saveLog('更新用户报名状态', $id);

        //获取报名信息
        $model = Answer::find()->where(['id' => $id])->with(['user', 'activity'])->one();
        //修改当前报名的状态为通过或者不通过

        //设置答案的状态为通过或不通过
        $model->apply_status = $status;
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
        }


        // 通过的总数
        $pass_count = Answer::find()
                        ->where(['activity_id' => $activity_id ])
                        ->andWhere(['status' => Answer::STATUS_REVIEW_PASS ])
                        ->count();

        // 迟到人数
        $arrive_late = Answer::find()
                        ->where(['activity_id' => $activity_id ])
                        ->andWhere(['arrive_status' => Answer::STATUS_ARRIVE_LATE,'status' => Answer::STATUS_REVIEW_PASS])
                        ->count();            
        // 请假人数
        $leave = Answer::find()
                        // ->select(['count(activity_id) as leave_count'])
                        ->where(['activity_id' => $activity_id ])
                        ->andWhere(['leave_status' => Answer::STATUS_LEAVE_YES,'status' => Answer::STATUS_REVIEW_PASS])
                        // ->groupBy('activity_id')
                        // ->asArray()
                        // ->one();  
                        ->count();
        // print_r($leave['leave_count']);

        //好评，差评，中评数统计
        $good_score = ActivityFeedback::find()
                        ->where(['activity_id'=>$activity_id])
                        ->andWhere('grade = 1')
                        ->count();
        $middle_score = ActivityFeedback::find()
                        ->where(['activity_id'=>$activity_id])
                        ->andWhere('grade = 2')
                        ->count();
        $bad_score = ActivityFeedback::find()
                        ->where(['activity_id'=>$activity_id])
                        ->andWhere('grade=3')         
                        ->count();
        $sponsor_count = ActivityFeedback::find()
                        ->where(['activity_id'=>$activity_id])
                        ->select('grade')
                        ->count();
         if($sponsor_count != 0){               
            $sponsor_sum = ActivityFeedback::find()
                        ->where(['activity_id'=>$activity_id])
                        ->select('sum(sponsor_start1) sponsor_start1,sum(sponsor_start2) sponsor_start2,sum(sponsor_start3) sponsor_start3')
                        ->asArray()
                        ->one();
                      
            $sponsor_score = (($sponsor_sum['sponsor_start1'])*0.4+($sponsor_sum['sponsor_start2'])*0.3+($sponsor_sum['sponsor_start3'])*0.3)/$sponsor_count;

        }else{
            $sponsor_score = 0;
        }
        // 爽约人数
        $arrive_no = Answer::find()
                        ->where(['activity_id' => $activity_id ])
                        ->andWhere(['arrive_status' => Answer::STATUS_ARRIVE_YET,'status' => Answer::STATUS_REVIEW_PASS])
                        ->count(); 
        if ($pass_count > 0) {
            $late_ratio = round($arrive_late / $pass_count,2) *100 ."%";
            $leave_ratio = round($leave / $pass_count,2) *100 ."%";
            $arrive_no_ratio = round($arrive_no / $pass_count,2) *100 ."%";
        }else {
            $late_ratio = "0%";
            $leave_ratio = "0%";
            $arrive_no_ratio = "0%";
        }


        foreach ($models as $key => $value) {

            // 反馈的次数
            $models[$key]['feedback_count'] = ActivityFeedback::find()
                            ->where('user_id =' .$value['user']['id'] )
                            ->count();

            // 迟到次数
            $models[$key]['arrive_late'] = Answer::find()
                            ->where('user_id = '.$value['user']['id'])
                            ->andWhere(['arrive_status' => Answer::STATUS_ARRIVE_LATE])
                            ->count();

            // 爽约次数
            $models[$key]['arrive_no'] = Answer::find()
                            ->where('user_id = '.$value['user']['id'])
                            ->andWhere(['arrive_status' => Answer::STATUS_ARRIVE_YET])
                            ->andWhere(['status' => Answer::STATUS_REVIEW_PASS])
                            ->andWhere(['leave_status' => Answer::STATUS_LEAVE_YET])
                            ->count();
            $models[$key]['late_ratio'] = $late_ratio;
            $models[$key]['leave_ratio'] = $leave_ratio;
            $models[$key]['arrive_no_ratio'] = $arrive_no_ratio;
        }
        return [
                'model' => $models, 
                'good_score' => $good_score,
                'middle_score'=>$middle_score,
                'bad_score' => $bad_score,
                'sponsor_score' => $sponsor_score 
                ]; 
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

        if (Answer::STATUS_REVIEW_YET == $answer['status']) {
            return $result = ['status' => '2','sms' => '还没有筛选不需要发通知!','wechatResult' =>';-)'];
        }
        

        $wechatResult = '';
        $result = '';
        // 用户的手机号码不为空, 并且手机号码是合法的手机号
        if (!empty($answer['user']['mobile']) && SomeetValidator::isTelNumber($answer['user']['mobile'])) {

            //手机号
            $mobile = $answer['user']['mobile'];

            //设置默认的短信为等待的短信内容
            // $smsData = NotificationTemplate::fetchWaitSmsData($answer['activity']['title']);

            //判断状态是通过
            if (Answer::STATUS_REVIEW_PASS == $answer['status']) {

                    //获取通过的短信内容
                    $smsData = NotificationTemplate::fetchSuccessSmsData($answer['activity']['start_time'], $answer['activity']['title']);

                    if ( Answer::STATUS_ARRIVE_YET != $answer['arrive_status'] && $answer['is_feedback'] == Answer::FEEDBACK_NO ) {
                        //获取需要反馈的短信内容
                        $smsData = NotificationTemplate::fetchNeedFeedbackSmsData($answer['activity']['title']);  
                    }
            } elseif (Answer::STATUS_REVIEW_REJECT == $answer['status']) {
                    //获取不通过的短信内容
                    $smsData = NotificationTemplate::fetchFailSmsData($answer['activity']['start_time'], $answer['activity']['title']);
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
                // $templateData = NotificationTemplate::fetchWaitWechatTemplateData($openid, $answer['activity']);
                //如果通过
                if (Answer::STATUS_REVIEW_PASS == $answer['status']) {
                    //获取通过的模板消息内容
                    $templateData = NotificationTemplate::fetchSuccessWechatTemplateData($openid, $answer['user'], $answer['activity']);
                    if ( Answer::STATUS_ARRIVE_YET != $answer['arrive_status'] && $answer['is_feedback'] == Answer::FEEDBACK_NO ) {
                        //获取需要反馈的短信内容
                        $templateData = NotificationTemplate::fetchNeedFeedbackWechatTemplateData($openid, $answer['user'], $answer['activity']);  
                        $wechatResult = $templateData['data']['first']['value'];
                        // print_r($templateData);
                        }
                } elseif (Answer::STATUS_REVIEW_REJECT == $answer['status']) {
                    //获取不通过的模板消息内容
                    $templateData = NotificationTemplate::fetchFailedWechatTemplateData($openid, $answer['user'], $answer['activity']);
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
                $wechat_template = '报名用户id: '.$answer['user']['id'].' 的用户短信发送失败或者没有绑定微信';
            }
        } else {
            //报一个错误, 用户手机号码有误, 无法发送短信
            Yii::error('报名用户id: '.$answer['user']['id'].' 的用户手机号码未设置, 或者设置的不正确');
            $smsData = '报名用户id: '.$answer['user']['id'].' 的用户手机号码未设置, 或者设置的不正确';
        }
        
        return $result = ['status' => '0','sms' => $smsData,'wechatResult' => $wechatResult];

    }


}

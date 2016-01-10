<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use dektrium\user\models\Account;
use someet\common\models\ActivityFeedback;
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
}

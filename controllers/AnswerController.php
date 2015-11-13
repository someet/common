<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Answer;
use someet\common\models\AnswerItem;
use someet\common\models\Question;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class AnswerController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Answer::find()->where(['id' => $id])->with(['user', 'activity'])->one();
        $data = Yii::$app->getRequest()->post();

        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        $mobile = $model->user->mobile;
        $activityName = $model->activity->title;
        if ($this->isTelNumber($mobile)) {
            if ($model->status == Answer::STATUS_REVIEW_PASS || $model->status == Answer::STATUS_REVIEW_REJECT) {
                if ($model->status == Answer::STATUS_REVIEW_PASS) {
                    $wechat_id = 'maxwelldu';
                    $template = "【Someet活动平台】您好，恭喜您报名的“{$activityName}”活动已通过筛选。具体事宜请您添加工作人员微信（微信号：{$wechat_id}）后会进行说明。添加时请注明活动名称，期待与您共同玩耍，系统短信，请勿回复。";
                } elseif ($model->status == Answer::STATUS_REVIEW_REJECT) {
                    $template = "【Someet活动平台】Someet用户您好，很抱歉您报名的“{$activityName}”活动未通过筛选。关于如何提高报名的成功率，这里有几个小tips，1.认真回答筛选问题； 2.尽早报名，每周二周三是活动推送时间，周四周五报名的成功概率会相对降低很多 3.自己发起活动，优质的发起人是有参与活动特权的哦~ 当然，您还可以添加我们的官方客服Someet小海豹（微信号：someetxhb）随时与我们联系。期待下次活动和你相遇。系统短信，请勿回复。";
                }
                $r = Yii::$app->yunpian->sendSms($mobile, $template);
                if (!$r) {
                } else {
                    Answer::updateAll(['is_send' => '1', 'send_at' => time()], ['id' => $model->id]);
                }
            }
        }

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }

        return Answer::find()
            ->where(['id' => $model->id])
            ->asArray()
            ->with('answerItemList')
            ->one();
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
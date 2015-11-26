<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Activity;
use someet\common\models\ActivityFeedback;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 活动反馈控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class ActivityFeedbackController extends BackendController
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
                    'update' => ['post'],
                    'view' => ['get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }

    /**
     * 活动反馈列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex($activity_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $feedback = ActivityFeedback::find()
            ->where(['activity_id' => $activity_id])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();

        return $feedback;
    }

    /**
     * 修改 反馈状态
     *
     * POST 提交到 /activity-feedback/update?id=10
     *
     * ~~~
     * {
     *   "stars": "3",
     *   "feedback": "这次活动感觉很好",
     * }
     * ~~~
     *
     *
     * @param $id
     * @return array
     *
     * 成功
     *
     * ~~~
     * {
     *   "success": "1",
     *   "data": {
     *     "id": 10,
     *     "stars": "5",
     *     "feedback": "这次活动",
     *     "status": 10
     *   },
     *   "status_code": 200
     * }
     * ~~~
     *
     * 失败
     *
     * ~~~
     * {
     *   "success": "0",
     *   "errmsg": "",
     *   "status_code": 422
     * }
     * ~~~
     *
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $data = Yii::$app->getRequest()->post();

        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }

        return $this->findModel($id);
    }

    /**
     * 查看单个活动反馈
     * @param integer $activity_id 活动ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionView($activity_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = ActivityFeedback::find()
            ->where(['activity_id' => $activity_id])
            ->with([
                'user',
            ])
            ->asArray()
            ->all();

        return $model;
    }

    /**
     * 查找活动反馈
     * @param integer $id 活动反馈ID
     * @return ActivityFeedback 活动反馈对象
     * @throws NotFoundHttpException 如果活动反馈找不到则抛出404异常
     */
    public function findModel($id)
    {
        $model = ActivityFeedback::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("反馈不存在");
        }
    }
}

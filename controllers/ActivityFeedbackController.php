<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use app\models\ActivityFeedback;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class ActivityFeedbackController extends Controller
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
                    'update' => ['post'],
                    'view' => ['get'],
                ],
            ],
        ];
    }

    /**
     * 活动反馈列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $types = ActivityFeedback::find()
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();

        return $types;
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
     * 删除反馈
     * POST 请求 /activity-feedkback/delete?id=10
     *
     * @param $id
     * @return array
     *
     * 成功
     *
     * ~~~
     * {
     *   "success": "1",
     *   "data": [],
     *   "status_code": 200
     * }
     * ~~~
     *
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        $model->status = ActivityFeedback::STATUS_DELETED;
        if ($model->save() === false) {
            throw new ServerErrorHttpException('删除失败');
        }

        return [];
    }

    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * @param $id
     * @return ActivityFeedback
     * @throws NotFoundHttpException
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

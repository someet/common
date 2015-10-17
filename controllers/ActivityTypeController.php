<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use app\models\Activity;
use app\models\ActivityType;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class ActivityTypeController extends Controller
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
            ],
        ];
    }

    /**
     * 活动类型列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $types = ActivityType::find()
            ->orderBy([
                'display_order' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $types;
    }

    /**
     * 添加一个活动类型
     *
     * POST 请求 /activity-type/create
     *
     * ~~~
     * {
     *   "name": <string: 活动名称>,
     *   "display_order": <int: 排序，此字段为空为默认值 99>
     * }
     * ~~~
     *
     * @return  array
     *
     * 失败
     *
     * ~~~
     * {
     * "success": "0",
     * "errmsg": "名称长度不得超过255个字符",
     * "status_code": 422
     * }
     * ~~~
     *
     * 成功
     *
     * {
     * "success": "1",
     * "data": {
     *   "id": 10,
     *   "name": "户外",
     *   "display_order": 99,
     *   "status": 10
     * },
     * "status_code": 200
     * }
     *
     * @throws DataValidationFailedException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        $data = $request->post();
        $model = new ActivityType;

        if ($model->load($data, '') && $model->save()) {
            return ActivityType::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改 类型
     *
     * POST 提交到 /activity-type/update?id=10
     *
     * ~~~
     * {
     *   "name": "户外1",
     *   "display_order": 96
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
     *     "name": "户外1",
     *     "display_order": 96,
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
     *   "errmsg": "名称最少含有2个字符",
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

        if (isset($data['name'])) {
            $model->name = $data['name'];
            if (!$model->validate('name')) {
                throw new DataValidationFailedException($model->getFirstError('name'));
            }
        }

        if (isset($data['display_order'])) {
            $model->display_order = $data['display_order'];
            if (!$model->validate('display_order')) {
                throw new DataValidationFailedException($model->getFirstError('display_order'));
            }
        }

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
     * 删除活动
     * POST 请求 /activity-type/delete?id=10
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

        // 检查该类型下是否有活动, 如果有则提示不能删除
        if (Activity::findOne(['type_id' => $id])) {
            throw new ServerErrorHttpException('当前分类下还有活动, 无法删除');
        }

        if ($model->delete() === false) {
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
     * @return ActivityType
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = ActivityType::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("类型不存在");
        }
    }
}

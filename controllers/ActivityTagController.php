<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\ActivityTag;
use someet\common\models\RTagActivity;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 活动标签控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class ActivityTagController extends BackendController
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
            ],
        ];

    }

    /**
     * 活动标签列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $entities = ActivityTag::find()
            ->orderBy([
                'frequency' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $entities;
    }

    /**
     * 添加一个活动标签
     *
     * POST 请求 /activity-type/create
     *
     * ~~~
     * {
     *   "label": <string: 活动标签>,
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
     * "errmsg": "标签长度不得超过255个字符",
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
     *   "label": "行为",
     *   "status": 0
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
        $model = new ActivityTag;

        if ($model->load($data, '') && $model->save()) {
            return ActivityTag::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改 标签
     *
     * POST 提交到 /activity-tag/update?id=10
     *
     * ~~~
     * {
     *   "label": "谈话",
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
     *     "label": "谈话",
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
     *   "errmsg": "标签最少含有2个字符",
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

        if (isset($data['label'])) {
            $model->label = $data['label'];
            if (!$model->validate('label')) {
                throw new DataValidationFailedException($model->getFirstError('label'));
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
     * POST 请求 /activity-tag/delete?id=10
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

        // 检查该标签下是否有活动, 如果有则提示不能删除
        if (RTagActivity::findOne(['tag_id' => $id])) {
            throw new ServerErrorHttpException('当前标签下还有活动, 无法删除');
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('删除失败');
        }

        return [];
    }

    /**
     * 查看一个活动标签对象
     * @param integer $id 活动标签id
     * @return ActivityTag 活动标签对象
     * @throws NotFoundHttpException 如果没有找到活动标签则抛出404异常
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * 返回json格式符合条件的标签列表
     * @param $query
     * @return array
     */
    public function actionList($query)
    {
        $models = ActivityTag::find()->where(['like', 'name', $query])->all();
        $items = [];

        foreach ($models as $model) {
            $items[] = ['text' => $model->name];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $items;
    }

    /**
     * 查找活动标签对象
     * @param integer $id 活动标签ID
     * @return ActivityTag 活动标签对象
     * @throws NotFoundHttpException 如果找不到活动标签对象, 则抛出活动异常
     */
    public function findModel($id)
    {
        $model = ActivityTag::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("活动标签不存在");
        }
    }
}

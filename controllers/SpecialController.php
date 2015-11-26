<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Special;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 专题控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class SpecialController extends BackendController
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
     * 专题列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $entities = Special::find()
            ->where(['>', 'status', 0])
            ->orderBy([
                'is_top' => SORT_DESC,
                'updated_at' => SORT_DESC,
            ])
            ->all();

        return $entities;
    }

    /**
     * 添加一个专题类型
     *
     * POST 请求 /special/create
     *
     * ~~~
     * {
     *   "title": <string: 专题标题>,
     *   "desc": <string: 专题副标题>,
     *   "poster": <string: 专题海报>,
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
     * "errmsg": "标题长度不得超过255个字符",
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
     *   "title": "国庆专题",
     *   "display_order": 99,
     *   "status": 20
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
        $model = new Special;

        if ($model->load($data, '') && $model->save()) {
            return Special::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改 专题
     *
     * POST 提交到 /special/update?id=10
     *
     * ~~~
     * {
     *   "title": "国庆专题2",
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
     *     "title": "国庆专题2",
     *     "display_order": 96,
     *     "status": 20
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
     *   "errmsg": "专题最少含有2个字符",
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

        if (isset($data['title'])) {
            $model->title = $data['title'];
            if (!$model->validate('title')) {
                throw new DataValidationFailedException($model->getFirstError('title'));
            }
        }

        if (isset($data['desc'])) {
            $model->desc = $data['desc'];
            if (!$model->validate('desc')) {
                throw new DataValidationFailedException($model->getFirstError('desc'));
            }
        }

        if (isset($data['poster'])) {
            $model->poster = $data['poster'];
            if (!$model->validate('poster')) {
                throw new DataValidationFailedException($model->getFirstError('poster'));
            }
        }

        if (isset($data['is_top'])) {
            $model->is_top = $data['is_top'];
            if (!$model->validate('is_top')) {
                throw new DataValidationFailedException($model->getFirstError('is_top'));
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
     * 删除专题
     * POST 请求 /special/delete?id=10
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
        $model->status = Special::STATUS_DELETED;
        if ($model->save() === false) {
            throw new ServerErrorHttpException('删除失败');
        }

        return [];
    }

    /**
     * 显示单个专题项
     * @param integer $id 专题ｉｄ
     * @return Special 专题对象
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * 查找一个专题
     * @param integer $id 专题ID
     * @return Special 专题对象
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Special::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("专题不存在");
        }
    }
}

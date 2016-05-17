<?php
namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\SpaceSection;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 场地区间控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class SpaceSectionController extends BackendController
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
            /*
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
            */
        ];
    }

    /**
     * 场地区间列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $types = SpaceSection::find()
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->asArray()
            ->all();

        return $types;
    }

    /**
     * 根据场地ID获取区间列表
     *
     * @param integer $spot_id 场地ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListBySpotId($spot_id = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($spot_id > 0) {
            $models = SpaceSection::find()
                ->where(['spot_id' => $spot_id])
                ->asArray()
                ->all();
        } else {
            $models = SpaceSection::find()
                ->asArray()
                ->all();
        }

        return $models;
    }


        /**
     * 添加一个场地区间
     *
     * POST 请求 /space-section/create
     *
     * ~~~
     * {
     *   "name": <string: 区间名称>,
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
        $model = new SpaceSection();

        if ($model->load($data, '') && $model->save()) {
            \someet\common\models\AdminLog::saveLog('场地区间添加成功', $model->primaryKey);
            return SpaceSection::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改 场地区间
     *
     * POST 提交到 /space-section/update?id=10
     *
     * ~~~
     * {
     *   "name": "户外1",
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

        if (isset($data['people'])) {
            $model->people = $data['people'];
            if (!$model->validate('people')) {
                throw new DataValidationFailedException($model->getFirstError('people'));
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

        \someet\common\models\AdminLog::saveLog('更新场地区间', $model->primaryKey);
        return $this->findModel($id);
    }

    /**
     * 删除场地区间
     * POST 请求 /space-section/delete?id=10
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

        $model->status = SpaceSection::STATUS_DELETE;
        if (!$model->save()) {
            throw new ServerErrorHttpException('删除失败');
        }

        \someet\common\models\AdminLog::saveLog('删除场地分类', $model->primaryKey);
        return [];
    }

    /**
     * 查看一个场地区间
     * @param integer $id 场地区间ID
     * @return SpaceSection 场地区间对象
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * 查找场地区间
     * @param integer $id 场地区间id
     * @return SpaceSection 场地区间对象
     * @throws NotFoundHttpException 如果找不到分类则抛出404异常
     */
    public function findModel($id)
    {
        $model = SpaceSection::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("场地区间不存在");
        }
    }
}

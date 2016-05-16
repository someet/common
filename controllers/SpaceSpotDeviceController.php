<?php
namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\RSpotDevice;
use someet\common\models\SpaceSpotDevice;
use someet\common\models\SpaceType;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 场地设备控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class SpaceSpotDeviceController extends BackendController
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
     * 场地设备列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $types = SpaceSpotDevice::find()
            ->with('spots')
            ->orderBy([
                'display_order' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->asArray()
            ->all();
        foreach($types as $key => $type) {
            $types[$key]['spot_count'] = count($type['spots']);
        }

        return $types;
    }

    /**
     * 添加一个场地设备
     *
     * POST 请求 /space-spot-device/create
     *
     * ~~~
     * {
     *   "name": <string: 场地名称>,
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
     *   "icon": "http://jkl.com",
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
        $model = new SpaceSpotDevice();

        if ($model->load($data, '') && $model->save()) {
            \someet\common\models\AdminLog::saveLog('场地设备添加成功', $model->primaryKey);
            return SpaceType::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改 设备
     *
     * POST 提交到 /space-spot-device/update?id=10
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

        if (isset($data['icon'])) {
            $model->icon = $data['icon'];
            if (!$model->validate('icon')) {
                throw new DataValidationFailedException($model->getFirstError('icon'));
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

        \someet\common\models\AdminLog::saveLog('更新场地设备', $model->primaryKey);
        return $this->findModel($id);
    }

    /**
     * 删除场地
     * POST 请求 /space-spot-device/delete?id=10
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

        // 检查该设备下是否有场地, 如果有则提示不能删除
        if (RSpotDevice::findOne(['device_id' => $id])) {
            throw new ServerErrorHttpException('当前设备下还有场地, 无法删除');
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('删除失败');
        }

        \someet\common\models\AdminLog::saveLog('删除场地设备', $model->primaryKey);
        return [];
    }

    /**
     * 查看一个场地设备
     * @param integer $id 场地设备ID
     * @return SpaceType 场地设备对象
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * 查找场地设备
     * @param integer $id 场地设备id
     * @return SpaceType 场地设备对象
     * @throws NotFoundHttpException 如果找不到设备则抛出404异常
     */
    public function findModel($id)
    {
        $model = SpaceSpotDevice::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("设备不存在");
        }
    }

}

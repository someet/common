<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\SpaceSpot;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\data\Pagination;

/**
 *
 * 活动控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class SpaceSpotController extends BackendController
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
                // 'allowActions' => [
                // 'update-all-prevent',
                // 'update-status',
                // 'filter-prevent',
                // ]
            ],
            */
        ];
    }

    /**
     * 活动列表
     * @param integer $id
     * @param string $scenario 场景
     * @param string $type 类型,例如黑白名单或所有名单
     * @param int $perPage 每页多少条
     * @param int $isWeek  是否是本周活动  0 本周 1 非本周
     * @return array|int|null|\yii\db\ActiveRecord|\yii\db\ActiveRecord[]
     */
    public function actionIndex($id = null, $scenario = null, $perPage = 20, $type = null, $isWeek = 0)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        // only show draft and release activities

        //$andwhere = ['in', 'status', [Activity::STATUS_DRAFT, Activity::STATUS_RELEASE, Activity::STATUS_PREVENT ,Activity::STATUS_SHUT]];


        if ($type>0) {
                $where = ['type_id' => $type];
                $query = SpaceSpot::find()
                    ->with([
                        'type',
                        'sections',
                    ])
                    ->asArray()
                    ->where($where);
        } else {
                $query = SpaceSpot::find()
                    ->with([
                        'type',
                        'sections',
                    ])
                    ->asArray();
        }

        if ($id) {
            $query = SpaceSpot::find()
                ->where(['id' => $id])
                ->with([
                    'type',
                    'sections',
                ])
                ->asArray()
                ->one();
        } elseif ($scenario == "total") {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage
            ]);

            return $pagination->totalCount;
        } elseif ($scenario == "page") {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage
            ]);

            $activities = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }
        return $activities;
    }


    /**
     * 搜索场地, 供给活动分配发起人的自动完成功能使用
     * @param string $name 名称
     * @return array
     */
    public function actionSearch($name)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $models = SpaceSpot::find()
            ->with([
                'type',
                'sections',
            ])
            //->join('LEFT JOIN', 'user', 'user.id = activity.created_by')
            ->where(
                ['like', 'name', $name]
            )
            ->orWhere(['like','detail',$name])
            ->asArray()
            ->all();

            return [
                'models' => $models,
            ];
        // $activityExists = $activity->exists();
        // $countQuery = clone $activity;
        // $pages = new Pagination(['totalCount' => $countQuery->count()]);
        // $models = $activity->offset($pages->offset)
        //     ->limit($pages->limit)
        //     ->asArray()
        //     ->all();
        // if ($activityExists) {
            // return [
            // 'status' => 1,
            // 'models' => $models,
            // 'pages' => $pages,
            // ];
        // } else {
        //     return [
        //         'status' => 0,
        //     ];
        // }


    }
    /**
     * 根据活动类型查询场地列表
     *
     * @param integer $type_id 场地类型ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListByTypeId($type_id = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // only show draft and release activities
        //$andwhere = ['in', 'status', [Activity::STATUS_DRAFT, Activity::STATUS_RELEASE, Activity::STATUS_PREVENT ,Activity::STATUS_SHUT]];

        if ($type_id > 0) {
            $activities = SpaceSpot::find()
                ->where(['type_id' => $type_id])
                ->with([
                    'type',
                    'sections',
                ])
                ->asArray()
                ->all();
        } else {
            $activities = Activity::find()
                ->with([
                    'type',
                    'sections',
                ])
                ->asArray()
                ->all();
        }

        return $activities;
    }

    /**
     * 场地列表
     *
     * @return array|\yii\db\ActiveRecord[]
    public function actionIndex()
    {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $activities = Activity::find()
    ->with([
    'type',
    ])
    ->orderBy([
    'is_top' => SORT_DESC,
    'updated_at' => SORT_DESC,
    'id' => SORT_DESC,
    ])
    ->all();

    return $activities;
    }
     */

    /**
     * 添加一个场地
     *
     * POST 请求 /space-spot/create
     *
     * ~~~
     * {
     *   "title": <string: 场地名称>,
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
     *   "displayorder": 99,
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

        $model = new SpaceSpot();

        if ($model->load($data, '') && $model->save()) {
            // 保存操作记录
            \someet\common\models\AdminLog::saveLog('添加场地', $model->primaryKey);
            return SpaceSpot::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改一个场地
     *
     * POST 提交到 /space-spot/update?id=10
     *
     * ~~~
     * {
     *   "title": "户外1",
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
     *     "title": "户外1",
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

        if (isset($data['area'])) {
            $model->area = $data['area'];
            if (!$model->validate('area')) {
                throw new DataValidationFailedException($model->getFirstError('area'));
            }
        }

        if (isset($data['address'])) {
            $model->address = $data['address'];
            if (!$model->validate('address')) {
                throw new DataValidationFailedException($model->getFirstError('address'));
            }
        }

        if (isset($data['type_id'])) {
            $model->type_id = $data['type_id'];
            if (!$model->validate('type_id')) {
                throw new DataValidationFailedException($model->getFirstError('type_id'));
            }
        }

        if (isset($data['image'])) {
            $model->image = $data['image'];
            if (!$model->validate('image')) {
                throw new DataValidationFailedException($model->getFirstError('image'));
            }
        }

        if (isset($data['router'])) {
            $model->router = $data['router'];
            if (!$model->validate('area')) {
                throw new DataValidationFailedException($model->getFirstError('router'));
            }
        }

        if (isset($data['map_pic'])) {
            $model->map_pic = $data['map_pic'];
            if (!$model->validate('address')) {
                throw new DataValidationFailedException($model->getFirstError('map_pic'));
            }
        }

        if (isset($data['detail'])) {
            $model->detail = $data['detail'];
            if (!$model->validate('details')) {
                throw new DataValidationFailedException($model->getFirstError('detail'));
            }
        }

        if (isset($data['contact'])) {
            $model->contact = $data['contact'];
            if (!$model->validate('contact')) {
                throw new DataValidationFailedException($model->getFirstError('contact'));
            }
        }

        if (isset($data['base_fee'])) {
            $model->base_fee = $data['base_fee'];
            if (!$model->validate('base_fee')) {
                throw new DataValidationFailedException($model->getFirstError('base_fee'));
            }
        }

        if (isset($data['principal'])) {
            $model->principal = $data['principal'];
            if (!$model->validate('principal')) {
                throw new DataValidationFailedException($model->getFirstError('principal'));
            }
        }

        if (isset($data['logo'])) {
            $model->logo = $data['logo'];
            if (!$model->validate('logo')) {
                throw new DataValidationFailedException($model->getFirstError('logo'));
            }
        }

        if (isset($data['owner'])) {
            $model->owner = $data['owner'];
            if (!$model->validate('owner')) {
                throw new DataValidationFailedException($model->getFirstError('owner'));
            }
        }

        if (isset($data['longitude'])) {
            $model->longitude = $data['longitude'];
            if (!$model->validate('longitude')) {
                throw new DataValidationFailedException($model->getFirstError('longitude'));
            }
        }

        if (isset($data['latitude'])) {
            $model->latitude = $data['latitude'];
            if (!$model->validate('latitude')) {
                throw new DataValidationFailedException($model->getFirstError('latitude'));
            }
        }

        if (isset($data['open_time'])) {
            $model->open_time = $data['open_time'];
            if (!$model->validate('open_time')) {
                throw new DataValidationFailedException($model->getFirstError('open_time'));
            }
        }

        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        //发起人
        if (isset($data['created_by'])) {
            $model->created_by = $data['created_by'];
            if (!$model->validate('created_by')) {
                throw new DataValidationFailedException($model->getFirstError('created_by'));
            }
        }

        //负责人(PMA)
        if (isset($data['principal'])) {
            $model->principal= $data['principal'];
            if (!$model->validate('principal')) {
                throw new DataValidationFailedException($model->getFirstError('principal'));
            }
        }

        //排序更新
        if (isset($data['display_order'])) {
            $model->display_order= $data['display_order'];
            if (!$model->validate('display_order')) {
                throw new DataValidationFailedException($model->getFirstError('display_order'));
            }
        }

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }
        \someet\common\models\AdminLog::saveLog('更新场地', $model->primaryKey);

        return $this->findModel($id);
    }

    /**
     * 删除场地
     * POST 请求 /space-spot/delete?id=10
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
        $model->status = Activity::STATUS_DELETE;
        if ($model->save() === false) {
            throw new ServerErrorHttpException('删除失败');
        }
        \someet\common\models\AdminLog::saveLog('删除场地', $model->primaryKey);

        return [];
    }

    /**
     * 查看单个场地详情
     * @param integer $id 场地ID
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = SpaceSpot::find()
            ->where(['id' => $id])
            ->with([
                'type',
                'sections',
            ])
            ->asArray()
            ->one();

        return $model;
    }

    /**
     * 查找场地
     * @param integer $id 场地ID
     * @return Activity 场地对象
     * @throws NotFoundHttpException 如果没有查找到则抛出404异常
     */
    public function findModel($id)
    {
        $model = SpaceSpot::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("场地不存在");
        }
    }
}

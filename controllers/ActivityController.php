<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Activity;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 活动控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class ActivityController extends BackendController
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
                    'list-by-type-id',
                ]
            ],
        ];
    }

    /**
     * 根据活动类型查询活动列表
     *
     * @param integer $type_id 活动类型ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListByTypeId($type_id=0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($type_id > 0) {
            $activities = Activity::find()
                ->where(['type_id' => $type_id])
                ->andWhere(['in', 'status', [Activity::STATUS_DRAFT, Activity::STATUS_RELEASE]])
                ->with([
                    'type',
                    'tags',
                    'question',
                    'answerList',
                    'feedbackList'
                ])
                ->asArray()
                ->orderBy([
                    'is_top' => SORT_DESC,
                    'updated_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ])
                ->all();
        } else {
            $activities = Activity::find()
                ->where(['in', 'status', [Activity::STATUS_DRAFT, Activity::STATUS_RELEASE]])
                ->with([
                    'type',
                    'tags',
                    'question',
                    'answerList',
                    'feedbackList'
                ])
                ->asArray()
                ->orderBy([
                    'is_top' => SORT_DESC,
                    'updated_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ])
                ->all();
        }
        foreach($activities as $key => $activity) {
            $activities[$key]['answer_count'] = count($activity['answerList']);
            $activities[$key]['feedback_count'] = count($activity['feedbackList']);
            $activities[$key]['preview_url'] = Yii::$app->params['domain'].'preview/'.$activity['id'];
            $activities[$key]['filter_url'] = Yii::$app->params['domain'].'filter/'.$activity['id'];
        }


        return $activities;
    }

    /**
     * 活动列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $activities = Activity::find()
            ->with([
                'type',
                'tags',
            ])
            ->orderBy([
                'is_top' => SORT_DESC,
                'updated_at' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $activities;
    }

    /**
     * 添加一个活动
     *
     * POST 请求 /activity/create
     *
     * ~~~
     * {
     *   "title": <string: 活动名称>,
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
        $model = new Activity;

        if ($model->load($data, '') && $model->save()) {
            // 保存操作记录
            \someet\common\models\AdminLog::saveLog($this->searchById($model->id), $model->primaryKey);
            return Activity::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改一个活动
     *
     * POST 提交到 /activity/update?id=10
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

        if (isset($data['peoples'])) {
            $model->peoples = $data['peoples'];
            if (!$model->validate('peoples')) {
                throw new DataValidationFailedException($model->getFirstError('peoples'));
            }
        }

        if (isset($data['cost'])) {
            $model->cost = $data['cost'];
            if (!$model->validate('cost')) {
                throw new DataValidationFailedException($model->getFirstError('cost'));
            }
        }

        if (isset($data['cost_list'])) {
            $model->cost_list = $data['cost_list'];
            if (!$model->validate('cost_list')) {
                throw new DataValidationFailedException($model->getFirstError('cost_list'));
            }
        }

        if (isset($data['start_time'])) {
            $model->start_time = $data['start_time'];
            if (!$model->validate('start_time')) {
                throw new DataValidationFailedException($model->getFirstError('start_time'));
            }
        }

        if (isset($data['end_time'])) {
            $model->end_time = $data['end_time'];
            if (!$model->validate('end_time')) {
                throw new DataValidationFailedException($model->getFirstError('end_time'));
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

        if (isset($data['details'])) {
            $model->details = $data['details'];
            if (!$model->validate('details')) {
                throw new DataValidationFailedException($model->getFirstError('details'));
            }
        }

        if (isset($data['poster'])) {
            $model->poster = $data['poster'];
            if (!$model->validate('poster')) {
                throw new DataValidationFailedException($model->getFirstError('poster'));
            }
        }

        if (isset($data['group_code'])) {
            $model->group_code = $data['group_code'];
            if (!$model->validate('group_code')) {
                throw new DataValidationFailedException($model->getFirstError('group_code'));
            }
        }

        if (isset($data['review'])) {
            $model->review = $data['review'];
            if (!$model->validate('review')) {
                throw new DataValidationFailedException($model->getFirstError('review'));
            }
        }

        if (isset($data['tagNames'])) {
            $model->tagNames = $data['tagNames'];
            if (!$model->validate('tagNames')) {
                throw new DataValidationFailedException($model->getFirstError('tagNames'));
            }
        }

        if (isset($data['is_top'])) {
            $model->is_top = $data['is_top'];
            if (!$model->validate('is_top')) {
                throw new DataValidationFailedException($model->getFirstError('is_top'));
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

        if (isset($data['type_id'])) {
            $model->type_id = $data['type_id'];
            if (!$model->validate('type_id')) {
                throw new DataValidationFailedException($model->getFirstError('type_id'));
            }
        }

        //发布活动的时候有值
        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        if (isset($data['edit_status'])) {
            $model->edit_status = $data['edit_status'];
            if (!$model->validate('edit_status')) {
                throw new DataValidationFailedException($model->getFirstError('edit_status'));
            }
        }

        if (isset($data['content'])) {
            $model->content = $data['content'];
            if (!$model->validate('content')) {
                throw new DataValidationFailedException($model->getFirstError('content'));
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

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }
        \someet\common\models\AdminLog::saveLog($this->searchById($model->id), $model->primaryKey);

        return $this->findModel($id);
    }

    /**
     * 删除活动
     * POST 请求 /activity/delete?id=10
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
        \someet\common\models\AdminLog::saveLog($this->searchById($model->id), $model->primaryKey);

        return [];
    }

    /**
     * 查看单个活动详情
     * @param integer $id 活动ID
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Activity::find()
            ->where(['id' => $id])
            ->with([
                'type',
                'user',
                'user.profile',
                'principal',
                'principal.profile',
                'tags'
            ])
            ->asArray()
            ->one();

        return $model;
    }

    /**
     * 查找活动
     * @param integer $id 活动ID
     * @return Activity 活动对象
     * @throws NotFoundHttpException 如果没有查找到则抛出404异常
     */
    public function findModel($id)
    {
        $model = Activity::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("活动不存在");
        }
    }

    public function searchById($id){
        if (($model = Activity::findOne($id)) !== null) {
            return json_encode($model->toArray());
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }
}

<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\User;
use someet\common\models\Activity;
use someet\common\models\RActivitySpace;
use someet\common\models\SpaceSection;
use someet\common\models\RActivityFounder;
use someet\common\models\AdminLog;
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
class FounderController extends BackendController
{
    private $activity_order = [
                        'is_top' => SORT_DESC,
                        'display_order' => SORT_ASC,
                        'id' => SORT_DESC,
                    ];
	public function actionIndexList($id = null, $scenario = null, $perPage = 20, $type = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		$user_id = Yii::$app->user->id;
		$query = Activity::find()
				->with([
                    'type',
                    'tags',
                    'question',
                    'user',
                    'answerList',
                    'feedbackList'
                    ])
				->where(['created_by' => $user_id]);

		$countQuery = clone $query;
        $pagination = new Pagination([
                    'totalCount' => $countQuery->count(),
                    'pageSize' => $perPage
                ]);

        $activities = $query->offset($pagination->offset)
        				->limit($pagination->limit)
        				->all();
		return $activities;

	}

     /**
     * 搜索活动, 供给活动分配发起人的自动完成功能使用
     * @param string $username 标题
     * @return array
     */
    public function actionSearch($title)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user_id = Yii::$app->user->id;
        $activity = Activity::find()
                    ->where(
                        ['like', 'title', $title]
                    )
                    ->andwhere(['created_by' => $user_id]);
                    // ->orWhere(['like','desc',$title])
                    // ->orWhere(['like','content',$title]);
        $activityExists = $activity->exists();
        $countQuery = clone $activity;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $activity->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        if ($activityExists) {
            return [
                'status' => 1,
                'models' => $models,
                'pages' => $pages,
            ];
        } else {
            return [
                'status' => 0,
            ];
        }


    }

    /**
     * 默认数据
     * @return 返回对象
     */
    public function actionDefaultData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user_id = Yii::$app->user->id;
        $user = User::findOne($user_id);
        return ['user' => $user];
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
        $user_id = Yii::$app->user->id;
        $user = User::findOne($user_id);

            $andwhere = ['in', 'status', [
            Activity::STATUS_FOUNDER_DRAFT,
            Activity::STATUS_RELEASE,
            Activity::STATUS_PREVENT,
            Activity::STATUS_SHUT,
            Activity::STATUS_CANCEL,
            Activity::STATUS_CHECK,
            Activity::STATUS_REFUSE,
            Activity::STATUS_PASS,

            ]];
    
            $query = Activity::find()
                    ->with([
                    'type',
                    'tags',
                    'question',
                    'user',
                    'answerList',
                    'feedbackList'
                    ])
                    ->where($andwhere)
                    ->andwhere(['created_by' => $user_id])
                    ->asArray()
                    ->orderBy($this->activity_order);

            if ($id) {
                $query = Activity::find()
                ->where(['id' => $id])
                ->with([
                    'type',
                    'question',
                    'answerList',
                    'feedbackList',
                    'user',
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
                foreach ($activities as $key => $activity) {
                    $activities[$key]['answer_count'] = count($activity['answerList']);
                    $activities[$key]['feedback_count'] = count($activity['feedbackList']);
                    $activities[$key]['preview_url'] = Yii::$app->params['domain'].'preview/'.$activity['id'];
                    $activities[$key]['filter_url'] = Yii::$app->params['domain'].'filter/'.$activity['id'];
                    //set last week days
                    $activities[$key]['this_week'] = getLastEndTime() < $activity['end_time'] ? 1 : 0;
                }
            }
            return ['model' => $activities, 'user' => $user];
    }

    /**
     * 创建活动
     * @return 活动对象
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        $data = $request->post();

        $start_time = isset($data['start_time']) ? $data['start_time'] : 0;
        $data['week'] = $start_time > 0 ? date('w', $start_time) : 0;
        $data['status'] = Activity::STATUS_FOUNDER_DRAFT;
        $model = new Activity;
        if ($model->load($data, '') && $model->save()) {
            // 添加发起人
            if (!empty($data['founder'])) {
                foreach ($data['founder'] as $founder) {
                    $r_activity_founder = new RActivityFounder();
                    $r_activity_founder->activity_id = $model->id;
                    $r_activity_founder->founder_id = $founder['id'];
                    $r_activity_founder->save();
                }
            }

            // 添加活动场地
            if (!empty($data['space_spot_id']) && isset($data['space_section_id'])) {
                if ($data['space_section_id'] > 0) {
                    foreach ($data['space_section_id'] as $space_section) {
                        $r_activity_space =new RActivitySpace();
                        $r_activity_space->activity_id = $model->id;
                        $r_activity_space->space_spot_id = $data['space_spot_id'];
                        $r_activity_space->space_section_id = $space_section;
                        $r_activity_space->save();
                    }
                } else {
                    $space_section = SpaceSection::find()
                                    ->where(['spot_id' => $data['space_spot_id']])
                                    ->asArray()
                                    ->all();
                    foreach ($space_section as $section) {
                        $r_activity_space =new RActivitySpace();
                        $r_activity_space->activity_id = $model->id;
                        $r_activity_space->space_spot_id = $data['space_spot_id'];
                        $r_activity_space->space_section_id = $section['id'];
                        $r_activity_space->save();
                    }
                }
            }
            // 保存操作记录
            \someet\common\models\AdminLog::saveLog('添加活动', $model->primaryKey);
            return Activity::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
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
                'dts',
                'dts.profile',
                'pma',
                'pma.profile',
                'cofounder1',
                'cofounder1.profile',
                'cofounder2',
                'space',
                'cofounder2.profile',
                'space.sections',
            ])
            ->asArray()
            ->one();

        return $model;
    }
    /**
     * 修改一个活动
     * @param $id
     * @return array
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user_id = Yii::$app->user->id;
        $model = Activity::findOne($id);
        if (empty($model)) {
            return "活动不存在！";
        } 
        // post提交的数据
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

            $start_time = $model->start_time;
            $model->week = $start_time > 0 ? date('w', $start_time) : 0;
        }

        if (isset($data['end_time'])) {
            $model->end_time = $data['end_time'];
            if (!$model->validate('end_time')) {
                throw new DataValidationFailedException($model->getFirstError('end_time'));
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

 
        if (isset($data['review'])) {
            $model->review = $data['review'];
            if (!$model->validate('review')) {
                throw new DataValidationFailedException($model->getFirstError('review'));
            }
        }

        if (isset($data['type_id'])) {
            $model->type_id = $data['type_id'];
            if (!$model->validate('type_id')) {
                throw new DataValidationFailedException($model->getFirstError('type_id'));
            }
        }

        if (isset($data['field2'])) {
            $model->field2 = $data['field2'];
            if (!$model->validate('field2')) {
                throw new DataValidationFailedException($model->getFirstError('field2'));
            }
        }
        //发布活动的时候有值
        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        if (isset($data['content'])) {
            $model->content = $data['content'];
            if (!$model->validate('content')) {
                throw new DataValidationFailedException($model->getFirstError('content'));
            }
        }

        if ($model->save()) {
            AdminLog::saveLog('更新活动', $model->primaryKey);
        }

        return $model;
    }
   
}
<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Activity;
use someet\common\models\RActivitySpace;
use someet\common\models\SpaceSection;
use someet\common\models\RActivityFounder;
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
     * 默认数据
     * @return 返回对象
     */
    public function actionDefaultData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user_id = Yii::$app->user->id;
        $user = User::findOne($user_id);
        return ['user_id' => $user];
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
            $andwhere = ['in', 'status', [
            Activity::STATUS_DRAFT,
            Activity::STATUS_RELEASE,
            Activity::STATUS_PREVENT,
            Activity::STATUS_SHUT,
            Activity::STATUS_CANCEL,
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
            return $activities;
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
}
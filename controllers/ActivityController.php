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
class ActivityController extends BackendController
{

    private $week = [
        0 => '周天',
        1 => '周一',
        2 => '周二',
        3 => '周三',
        4 => '周四',
        5 => '周五',
        6 => '周六',
    ];

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
                // 'allowActions' => [
                // 'update-all-prevent',
                // 'update-status',
                // 'filter-prevent',
                // 'add-founder',
                // ]
            ],
        ];
    }

    private $activity_order = [
                        'is_top' => SORT_DESC,
                        'display_order' => SORT_ASC,
                        'id' => SORT_DESC,
                    ];

    /**
     * 一键发布 所有预发布的活动
     * @return
     */
    public function actionUpdateAllPrevent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 查出所有预发布的活动
        
        $activities = Activity::find()
                    ->with([
                        'type',
                        'tags',
                        'question',
                        'user',
                        'answerList',
                        'feedbackList'
                    ])
                    ->where([
                        'activity.status' => Activity::STATUS_PREVENT,
                        ])
                    //->andWhere(['question.activity_id' => $activity])
                    ->asArray()
                    ->all();



        if (!empty($activities)) {
            foreach ($activities as $activity) {
                if ($activity['question']) {
                    Activity::updateAll(['status' => Activity::STATUS_RELEASE], ['id' => $activity['id']]);
                    // $activity->save();
                    $activity['status'] = Activity::STATUS_RELEASE;
                }
            }
        }

        // return Activity::find()->where(['status' => Activity::STATUS_PREVENT])->all();
        return $activities;

    }

    /**
     * 活动状态更新 更新预发布，与草稿之间切换
     * @param  integer $id
     * @param  int $status
     * @return
     */
    public function actionUpdateStatus($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $activity = Activity::findOne($id);
        $activity->status = $status;
        $activity->save();
        return $activity;

    }

    /**
     * 查询过滤预发布活动
     * @param  integer $id
     * @param  int $status
     * @return
     */
    public function actionFilterPrevent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $activity = Activity::find()
                    ->with([
                        'type',
                        'tags',
                        'question',
                        'user',
                        'answerList',
                        'feedbackList'
                    ])
                    ->where(['status' => Activity::STATUS_PREVENT])
                    ->asArray()
                    ->all();
        return $activity;
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

            $andwhere = ['in', 'status', [
            Activity::STATUS_DRAFT,
            Activity::STATUS_RELEASE,
            Activity::STATUS_PREVENT,
            Activity::STATUS_SHUT,
            Activity::STATUS_CANCEL,
            ]];
    
            if ($type>0) {
                //判断周末非周末
                if ($isWeek == 0) {
                    $where = ['type_id' => $type];
                    $query = Activity::find()
                    ->with([
                    'type',
                    'tags',
                    'question',
                    'user',
                    'answerList',
                    'feedbackList'
                    ])
                    ->asArray()
                    ->where($where)
                    ->andWhere('start_time > '.getLastEndTime())
                    ->andWhere($andwhere)
                    ->orderBy($this->activity_order);
                } else {
                    $where = ['type_id' => $type];
                    $query = Activity::find()
                    ->with([
                    'type',
                    'question',
                    'user',
                    'answerList',
                    'feedbackList'
                    ])
                    ->asArray()
                    ->where($where)
                    ->andWhere('start_time < '.getLastEndTime())
                    ->andWhere($andwhere)
                    ->orderBy($this->activity_order);
                }
            } else {
                //判断周末非周末
                if ($isWeek == 0) {
                    $query = Activity::find()
                    ->with([
                    'type',
                    'question',
                    'user',
                    'answerList',
                    'feedbackList'
                    ])
                    ->where($andwhere)
                    ->andWhere('start_time > '.getLastEndTime())
                    ->asArray()
                    ->orderBy($this->activity_order);
                } else {
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
                    ->andWhere('start_time < '.getLastEndTime())
                    ->asArray()
                    ->orderBy($this->activity_order);
                }
            }

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
     * 搜索活动, 供给活动分配发起人的自动完成功能使用
     * @param string $username 标题
     * @return array
     */
    public function actionSearch($title)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $activity = Activity::find()
                    ->with([
                            'type',
                            'tags',
                            'question',
                            'user',
                            'answerList',
                            'feedbackList'
                        ])
                    ->join('LEFT JOIN', 'user', 'user.id = activity.created_by')
                    ->where(
                        ['like', 'title', $title]
                    )
                    ->orWhere(['like','desc',$title])
                    ->orWhere(['like','content',$title])
                    ->orWhere(['like','user.username',$title]);
        $activityExists = $activity->exists();
        $countQuery = clone $activity;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $activity->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();
        foreach ($models as $key => $activity) {
            $models[$key]['answer_count'] = count($activity['answerList']);
            $models[$key]['feedback_count'] = count($activity['feedbackList']);
        }
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
     * 根据活动类型查询活动列表
     *
     * @param integer $type_id 活动类型ID
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionListByTypeId($type_id = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // only show draft and release activities
        $andwhere = ['in', 'status', [Activity::STATUS_DRAFT, Activity::STATUS_RELEASE, Activity::STATUS_PREVENT ,Activity::STATUS_SHUT]];

        if ($type_id > 0) {
            $activities = Activity::find()
                ->where(['type_id' => $type_id])
                ->andWhere($andwhere)
                ->with([
                    'type',
                    'question',
                    'answerList',
                    'feedbackList'
                ])
                ->asArray()
                ->orderBy($this->activity_order)
                ->all();
        } else {
            $activities = Activity::find()
                ->where($andwhere)
                ->with([
                    'type',
                    'question',
                    'answerList',
                    'feedbackList'
                ])
                ->asArray()
                ->orderBy($this->activity_order)
                ->all();
        }
        foreach ($activities as $key => $activity) {
            $activities[$key]['answer_count'] = count($activity['answerList']);
            $activities[$key]['feedback_count'] = count($activity['feedbackList']);
            $activities[$key]['preview_url'] = Yii::$app->params['domain'].'preview/'.$activity['id'];
            $activities[$key]['filter_url'] = Yii::$app->params['domain'].'filter/'.$activity['id'];

            //set last week days
            $activities[$key]['this_week'] = getLastEndTime() < $activity['end_time'] ? 1 : 0;
        }

        return $activities;
    }

    /**
     * 活动列表
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

        // 如果改变了报名总数的时候，修改一下活动的是否报满的这个字段
        if (isset($data['peoples'])) {
            $model->peoples = $data['peoples'];
            if (!$model->validate('peoples')) {
                throw new DataValidationFailedException($model->getFirstError('peoples'));
            }

            //查询现在的活动人数是否已经报满
            $activity = Activity::findOne($id);
            $is_full = $activity->join_people_count < $data['peoples'] ? Activity::IS_FULL_NO : Activity::IS_FULL_YES;

            //尝试更新活动是否已报名完成字段
            //如果 is_full 和之前的值一样则无需要更新
            if ($is_full != $activity->is_full) {
                //如果活动更新成功，更新活动当更新成功返回0,所以取反表示活动更新成功
                if (0 == Activity::updateAll(['is_full' => $is_full], ['id' => $id])) {
                    //更新错误
                    throw new DataValidationFailedException('更新活动数量失败');
                }
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

        //DTS
        if (isset($data['updated_by'])) {
            $model->updated_by = $data['updated_by'];
            if (!$model->validate('updated_by')) {
                throw new DataValidationFailedException($model->getFirstError('updated_by'));
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

        //负责人(PMA) 类型
        if (isset($data['pma_type'])) {
            $model->pma_type = $data['pma_type'];
            if (!$model->validate('pma_type')) {
                throw new DataValidationFailedException($model->getFirstError('pma_type'));
            }
        }

        //排序更新
        if (isset($data['display_order'])) {
            $model->display_order= $data['display_order'];
            if (!$model->validate('display_order')) {
                throw new DataValidationFailedException($model->getFirstError('display_order'));
            }
        }
        //扩展字段一
        if (isset($data['field1'])) {
            $model->field1= $data['field1'];
            if (!$model->validate('field1')) {
                throw new DataValidationFailedException($model->getFirstError('field1'));
            }
        }
        //扩展字段二
        if (isset($data['field2'])) {
            $model->field2= $data['field2'];
            if (!$model->validate('field2')) {
                throw new DataValidationFailedException($model->getFirstError('field2'));
            }
        }
        //扩展字段三
        if (isset($data['field3'])) {
            $model->field1= $data['field3'];
            if (!$model->validate('field3')) {
                throw new DataValidationFailedException($model->getFirstError('field3'));
            }
        }
        //扩展字段四
        if (isset($data['field4'])) {
            $model->field1= $data['field4'];
            if (!$model->validate('field4')) {
                throw new DataValidationFailedException($model->getFirstError('field4'));
            }
        }
        //扩展字段五
        if (isset($data['field5'])) {
            $model->field1= $data['field5'];
            if (!$model->validate('field5')) {
                throw new DataValidationFailedException($model->getFirstError('field5'));
            }
        }
        //扩展字段六
        if (isset($data['field6'])) {
            $model->field1= $data['field6'];
            if (!$model->validate('field6')) {
                throw new DataValidationFailedException($model->getFirstError('field6'));
            }
        }
        //扩展字段七
        if (isset($data['field7'])) {
            $model->field1= $data['field7'];
            if (!$model->validate('field7')) {
                throw new DataValidationFailedException($model->getFirstError('field7'));
            }
        }
        //扩展字段八
        if (isset($data['field8'])) {
            $model->field1= $data['field8'];
            if (!$model->validate('field8')) {
                throw new DataValidationFailedException($model->getFirstError('field8'));
            }
        }
        //联合发起人1
        if (isset($data['co_founder1'])) {
            $model->co_founder1= $data['co_founder1'];
            if (!$model->validate('co_founder1')) {
                throw new DataValidationFailedException($model->getFirstError('co_founder1'));
            }
        }
        //联合发起人2
        if (isset($data['co_founder2'])) {
            $model->co_founder2= $data['co_founder2'];
            if (!$model->validate('co_founder2')) {
                throw new DataValidationFailedException($model->getFirstError('co_founder2'));
            }
        }
        //联合发起人3
        if (isset($data['co_founder3'])) {
            $model->co_founder3= $data['co_founder3'];
            if (!$model->validate('co_founder3')) {
                throw new DataValidationFailedException($model->getFirstError('co_founder3'));
            }
        }
        //联合发起人4
        if (isset($data['co_founder4'])) {
            $model->co_founder4= $data['co_founder4'];
            if (!$model->validate('co_founder4')) {
                throw new DataValidationFailedException($model->getFirstError('co_founder4'));
            }
        }

        if (isset($data['space_spot_id'])) {
            $model->space_spot_id= $data['space_spot_id'];
            if (!$model->validate('space_spot_id')) {
                throw new DataValidationFailedException($model->getFirstError('space_spot_id'));
            }
        }

        if ($model->save()) {
            // 添加发起人
            if (!empty($data['founder'])) {
                $delete_founder = RActivityFounder::deleteAll(['activity_id'=> $model->id]);
                foreach ($data['founder'] as $founder) {
                    $r_activity_founder = new RActivityFounder();
                    $r_activity_founder->activity_id = $model->id;
                    $r_activity_founder->founder_id = $founder['id'];
                    $r_activity_founder->save();
                }
            }


            // 当场地id不为空时
            if (!empty($data['space_spot_id']) && isset($data['space_section_id'])) {
                // 当空间没有选择时默认存储全部
                if ($data['space_section_id'] > 0) {
                    $delete_spaces = RActivitySpace::deleteAll([
                        'activity_id'=> $model->id,
                        ]);
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
        } else {
            throw new ServerErrorHttpException();
        }


        \someet\common\models\AdminLog::saveLog('更新活动', $model->primaryKey);

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
        \someet\common\models\AdminLog::saveLog('删除活动', $model->primaryKey);

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
        // 发起人
        $founder = RActivityFounder::find()
                    ->with(['user','user.profile'])
                    ->where(['activity_id' => $id])
                    ->asArray()
                    ->all();
        // print_r($founder);
        // die;
        // 场地
        $section = RActivitySpace::find()
                    ->where(['activity_id' => $id, 'space_spot_id' => $model['space_spot_id']])
                    ->asArray()
                    ->all();

        $space_section = [];
        $new_founder = [];

        // die;
        
        foreach ($section as $key => $value) {
            $space_section[$key] = $value['space_section_id'];
        }

        foreach ($founder as $key => $value) {
            $new_founder[$key] = $value['user'];
        }
        // print_r($new_founder);
        // die;
        // die;
        foreach ($model as $key => $value) {
            $model['sections'] = $section;
            $model['founder'] = $new_founder;
        }
        // echo "<pre>";
        // print_r($model);

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
}

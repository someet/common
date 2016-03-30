<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Profile;
use someet\common\models\User;
use someet\common\models\Activity;
use someet\common\models\Answer;
use someet\common\models\YellowCard;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * 联系人控制器.
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 */
class MemberController extends BackendController
{
    //允许CSRF验证关闭
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
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
                    'yellow-card' => ['get'],
                    'update-category' => ['get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
                'allowActions' => [
                   'index',
                   'page',
                   'update-category'
                ]
            ],
        ];
    }

    /**
     * 更新用户的角色.
     *
     * @param int    $user_id       需要更新的用户ID
     * @param string $role_name     更新的角色名称
     * @param int    $assign_or_not 是赋权还是撤权
     *
     * @return array
     */
    public function actionUpdateAssignment($user_id, $role_name, $assign_or_not)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        //参数检查
        if ($user_id < 1 || empty($role_name) || !in_array($assign_or_not, [0, 1])) {
            return ['msg' => '参数不正确'];
        }

        //获取权限管理组件
        $auth = Yii::$app->authManager;

        //获取角色列表
        $roles = $auth->getRoles();

        //角色判断
        if (!array_key_exists($role_name, $roles)) {
            //提示角色不存在
            return ['msg' => '角色'.$role_name.'不存在'];
        }

        //根据角色名获取对象的角色对象
        $role = $auth->getRole($role_name);

        //判断是赋权还是撤权
        if ($assign_or_not) {

            //判断当前状态是未赋权的状态再进行赋权, 否则提示已经赋权
            if ($auth->getAssignment($role_name, $user_id)) {

                //提示已经授权, 无需再进行赋权
                return ['msg' => '该角色已经赋权'];
            }

            //如果上面不成立则表示未赋权, 可以尝试进行赋权
            if ($auth->assign($role, $user_id)) {
                return ['msg' => '更新角色成功'];
            } else {
                return ['msg' => '更新角色失败'];
            }
        } else {

            //判断当前状态是否有该权限, 如果没有则不能进行撤权
            if (!$auth->getAssignment($role_name, $user_id)) {

                //提示当前用户没有该角色权限, 无法撤权
                return ['msg' => '当前用户没有该角色权限, 无法撤权'];
            }

            //如果上面的不成立表示有权限，可以尝试进行撤权
            if ($auth->revoke($role, $user_id)) {

                //提示撤权成功
                return ['msg' => '撤权成功'];
            } else {

                //提示撤权失败
                return ['msg' => '撤权失败'];
            }
        }
    }

    /**
     * 设置用户为白名单.
     *
     * @param int    $user_id       用户ID
     * @param string $in_white_list 是否是白名单 'true' 和 'false'
     *
     * @return array|bool
     */
    public function actionSetUserInWhiteList($user_id, $in_white_list = 'true')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (1 == User::updateAll(['in_white_list' => $in_white_list == 'true' ? User::WHITE_LIST_YES : User::WHITE_LIST_NO], ['id' => $user_id])) {
            return [];
        } else {
            return false;
        }
    }

    /**
     * 联系人列表.
     *
     * @param int    $id
     * @param string $scenario 场景
     * @param string $type     类型,例如黑白名单或所有名单
     * @param int    $perPage  每页多少条
     *
     * @return array|int|null|\yii\db\ActiveRecord|\yii\db\ActiveRecord[]
     */
    public function actionIndex($id = null, $scenario = null, $perPage = 20, $type = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        switch ($type) {

            //黑名单用户列表
            case 'black':
                $where = ['status' => User::STATUS_ACTIVE, 'in_black_list' => User::BLACK_LIST_YES];
                $query = User::find()
                    ->with(['profile'])
                    ->asArray()
                    ->where($where)
                    ->orderBy(['id' => SORT_DESC]);
                break;

            //白名单用户列表
            case 'white':
                $where = ['status' => User::STATUS_ACTIVE, 'in_white_list' => User::WHITE_LIST_YES];
                $query = User::find()
                    ->with(['profile'])
                    ->asArray()
                    ->where($where)
                    ->orderBy(['id' => SORT_DESC]);
                break;

            //不同权限的用户列表
            case 'pma':
            case 'founder':
            case 'admin':
                $query = User::find()
                    ->joinWith('assignment')
                    ->where([
                        'status' => User::STATUS_ACTIVE,
                        'auth_assignment.item_name' => $type,
                    ])
                    ->with(['profile'])
                    ->orderBy(['id' => SORT_DESC])
                    ->asArray();

                break;

            //已删除的用户
            case 'delete':
                $where = ['status' => User::STATUS_DELETED];
                $query = User::find()
                    ->with(['profile'])
                    ->asArray()
                    ->where($where)
                    ->orderBy(['id' => SORT_DESC]);
                break;
            //已删除的用户
            case 'appeal':
                // $where = ['appeal_status' => YellowCard::APPEAL_STATUS_YES];
                $userAppeal = YellowCard::find()
                ->select('user_id')
                ->where(['appeal_status' => YellowCard::APPEAL_STATUS_YES])
                ->groupBy('user_id')
                ->asArray()
                ->all();
                $userArr = [];
                foreach ($userAppeal as $key => $value) {
                    $userArr[$key] = $value['user_id'];
                }
                // print_r($userArr);
                $query = User::find()
                    ->with(['profile'])
                    ->asArray()
                    ->where(['id' => $userArr])
                    ->orderBy(['id' => SORT_DESC]);
                break;
            //全部用户列表
            default:
                $where = ['status' => User::STATUS_ACTIVE];
                $query = User::find()
                    ->with(['profile'])
                    ->asArray()
                    ->where($where)
                    ->orderBy(['id' => SORT_DESC]);
                break;
        }

        if ($id) {
            $users = User::find()->where(['id' => $id])->with(['profile', 'assignment'])->asArray()->one();
            // $joinActity = Answer::find()
            //         ->where(['user_id' => $id ])
            //         ->with(['user','user.profile','user.assignment'])
            //         ->asArray()
            //         ->one();
        } elseif ($scenario == 'total') {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage,
            ]);

            return $pagination->totalCount;
        } elseif ($scenario == 'page') {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage,
            ]);

            $users = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }

        return $users;
    }

    /**
     * 用户参加过的活动.
     *
     * @param $user_id 用户的id
     */
    public function actionUserJoinActivities($user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Answer::find()->where(['user_id' => $user_id]);
        $pages = new Pagination(['totalCount' => $data->count()]);
        $answers = $data->offset($pages->offset)->limit($pages->limit)
            ->with([
                'activity',
                'activity.user',
                'activity.user.profile',
                'activity.type',
            ])
            ->asArray()
            ->orderBy([
                'updated_at' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        if ($answers) {
            return ['answers' => $answers, 'pages' => $pages];
        } else {
            return ['answers' => null, 'pages' => $pages];
        }
    }

    /**
     * 用户获得的黄牌.
     *
     * @param [int] $user_id [用户id]
     *
     * @return [type] [黄牌列表]
     */
    public function actionYellowCard($user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yellow_card = YellowCard::find()
                        ->where(['user_id' => $user_id])
                        ->all();

        return $yellow_card;
    }

    /**
     *更新黄牌种类.
     */
    public function actionUpdateCategory($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 选择类别（黄牌原因理由）1 迟到 2请假1  3请假2 4爽约 5带人 6骚扰
        switch ($status) {
            case '0':
                // 迟到
                $card_num = YellowCard::CARD_CATEGOTY_CANCEL;
                break;

            case '1':
                // 迟到
                $card_num = YellowCard::CARD_NUM_LATE;
                break;

            case '2':
                // 请假1
                $card_num = YellowCard::CARD_NUM_LEAVE_1;
                break;

            case '3':
                // 请假2
                $card_num = YellowCard::CARD_NUM_LEAVE_2;
                break;

            case '4':
                // 爽约
                $card_num = YellowCard::CARD_NUM_NO;
                break;

            case '5':
                // 带人
                $card_num = YellowCard::CARD_NUM_BRING;
                break;

            case '6':
                // 骚扰
                $card_num = YellowCard::CARD_NUM_ANNOY;
                break;

            default:
                $card_num = 0;
                break;

        }
        // 用户的id
        $user_id = Yii::$app->user->id;
        $yellow_card = YellowCard::findOne($id);
        $yellow_card->handle_user_id = $user_id;
        $yellow_card->card_category = $status;
        $yellow_card->card_num = $card_num;
        if ($yellow_card->appeal_status == YellowCard::APPEAL_STATUS_YES ) {
            $yellow_card->appeal_status = YellowCard::APPEAL_STATUS_COMPLETE;
        }
        if ($yellow_card->save()) {
            $userInfo = User::findOne($yellow_card->user_id);

            // 如果用户处于黑牌状态情况下 当黄牌数量少于3个则改变黑牌状态
            // if ($userInfo->black_label == User::BLACK_LIST_YES) {
                $count_yellow = YellowCard::find()
                                ->select('id,user_id , sum(card_num) card_count')
                                ->where(['user_id' => $yellow_card->user_id])
                                // ->andWhere(['status' => YellowCard::STATUS_NORMAL])
                                ->andwhere('card_category > 0')
                                // 上周一凌晨到本周一凌晨
                                ->andWhere('created_at > (' .getLastEndTime().' - 2419200) and '.'created_at < ' .getLastEndTime())
                                ->asArray()
                                ->groupBy('user_id')
                                ->all();
                if (!empty($count_yellow)) {
                     foreach ($count_yellow as  $value) {
                        if ($value['card_count'] < 3) {
                            User::updateAll([
                                'black_label' => User::BLACK_LIST_NO,
                                'black_time' => time(),
                                ],['id' => $value['user_id']]);

                            }
                     }
                 }
                // if (!empty($count_yellow)) {
                //    if ($count_yellow['card_count'] < 3) {
                //        User::updateAll([
                //            'black_label' => User::BLACK_LIST_NO,
                //            'black_time' => time(),
                //            ],['id' => $count_yellow['user_id']
                //        ]);
                //    }
                // }
            
        }

        return $yellow_card;
    }

    /**
     * 黄牌弃用 取消.
     *
     * @param [int] $id     [description]
     * @param [int] $status [黄牌状态]
     *
     * @return [type] [description]
     */
    public function actionAbandonYellowCard($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 用户的id
        $user_id = Yii::$app->user->id;
        $yellow_card = YellowCard::findOne($id);
        $yellow_card->status = YellowCard::STATUS_ABANDON;
        $yellow_card->handle_user_id = $user_id;
        $yellow_card->appeal_status = YellowCard::APPEAL_STATUS_COMPLETE;
        $yellow_card->save();

        return $yellow_card;
    }

    /**
     * 黄牌驳回.
     *
     * @param [type] $id           [黄牌id]
     * @param [type] $handle_reply [黄牌驳回填写的理由]
     *
     * @return [type] [黄牌的返回列表]
     */
    public function actionRejectYellowCard($id, $handle_reply = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 用户的id
        $user_id = Yii::$app->user->id;
        $yellow_card = YellowCard::findOne($id);
        $yellow_card->handle_user_id = $user_id;
        $yellow_card->appeal_status = YellowCard::APPEAL_STATUS_REJECT;
        $yellow_card->handle_reply = $handle_reply;
        $yellow_card->save();

        return $yellow_card;
    }

    /**
     * 黄牌申诉列表.
     *
     * @return [type] [description]
     */
    public function actionAppealList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yellow_card = YellowCard::find()
                        ->where(['appeal_status' => YellowCard::APPEAL_STATUS_YES])
                        // ->andWhere('created_at > ' .getWeekBefore().' and '.'created_at < ' .getLastEndTime())
                        ->count();

        return $yellow_card;
    }

    /**
     *pma参与的活动
     *发起人发起的活动.
     *
     *@param $role string admin | pma
     *@param $user_id 用户的id
     */
    public function actionActivityByRole($user_id, $role)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!in_array($role, ['founder', 'pma'])) {
            return [
                'code' => '0',
                'msg' => '参数错误',
                ];
        }

        $where = 'status>'.Activity::STATUS_DELETE;
        if ($role == 'pma') {
            $data = Activity::find()
                    ->where($where)
                    ->andWhere(['principal' => $user_id]);
        } elseif ($role == 'founder') {
            $data = Activity::find()
                    ->where($where)
                    ->andWhere(['created_by' => $user_id]);
        }

        $pages = new Pagination(['totalCount' => $data->count()]);
        $activities = $data->offset($pages->offset)->limit($pages->limit)
                ->with([
                    'user',
                    'user.profile',
                    'type',
                    'answerList',
                ])
                ->asArray()
                ->orderBy([
                    'updated_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ])
                ->all();

        if ($activities) {
            return ['answers' => $activities, 'pages' => $pages];
        } else {
            return ['answers' => null, 'pages' => $pages];
        }
    }

    /**
     * 搜索用户, 供给活动分配发起人的自动完成功能使用.
     *
     * @param string $username 用户名
     *
     * @return array
     */
    public function actionSearch($username)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()
            ->where(
                ['like', 'username', $username]
            )
            ->orWhere(
                ['like', 'mobile', $username]
            )
            ->andWhere([
                'status' => User::STATUS_ACTIVE,
            ])
            ->with(['profile'])
            ->limit(50)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        return $users;
    }

    /**
     * 搜索PMA, 供通知时使用.
     *
     * @param string $username 用户名
     * @param string $auth     权限, 是什么用户
     *
     * @return array
     */
    public function actionSearchByAuth($username, $auth = 'pma')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()
            ->joinWith('assignment')
            ->where([
                'status' => User::STATUS_ACTIVE,
                'auth_assignment.item_name' => $auth,
            ])
            ->andWhere(
                ['like', 'username', $username]
            )
            ->with(['profile', 'assignment'])
            ->limit(50)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        return $users;
    }

    /**
     * 创建一个联系人.
     *
     * @return null|static
     *
     * @throws DataValidationFailedException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();

        $user = new User();
        $user->setScenario('signup');

        if ($user->load($post, '') && $user->save()) {
            return User::findOne($user->id);
        } elseif ($user->hasErrors()) {
            $errors = $user->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 更新一个联系人.
     *
     * @param int $id 联系人ID
     *
     * @return null|static
     *
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();

        $model = $this->findOne($id);

        if (!empty($data['email'])) {
            $model->email = $data['email'];
            if (!$model->validate('email')) {
                throw new DataValidationFailedException($model->getFirstError('email'));
            }
        }

        if (isset($data['username'])) {
            $model->username = $data['username'];
            if (!$model->validate('username')) {
                throw new DataValidationFailedException($model->getFirstError('username'));
            }
        }

        if (isset($data['password']) && $data['password'] != '') {
            $model->password = $data['password'];
        }

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }

        if (isset($data['bio'])) {
            $profile = Profile::find()->where(['user_id' => $model->id])->one();
            $profile->bio = $data['bio'];

            if (!$profile->validate('bio')) {
                throw new DataValidationFailedException($profile->getFirstError('bio'));
            }

            if (!$profile->save()) {
                throw new ServerErrorHttpException();
            }
        }

        if (isset($data['headimgurl'])) {
            $profile = Profile::find()->where(['user_id' => $model->id])->one();
            $profile->headimgurl = $data['headimgurl'];

            if (!$profile->validate('headimgurl')) {
                throw new DataValidationFailedException($profile->getFirstError('headimgurl'));
            }

            if (!$profile->save()) {
                throw new ServerErrorHttpException();
            }
        }

        \someet\common\models\AdminLog::saveLog('更新联系人', $model->primaryKey);

        return $this->findOne($id);
    }

    /**
     *  更新用户profile表.
     *
     * @param int $id 联系人ID
     *
     * @return
     */
    public function updateProfile($id, $profile)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();

        $model = $this->findProfile($id);

        if (isset($data['headimgurl'])) {
            $model->headimgurl = $data['headimgurl'];
        }

        return $this->findProfile($id);
    }

    /**
     * 查找一个联系人.
     *
     * @param int $id 联系人ID
     *
     * @return null|static
     *
     * @throws NotFoundHttpException 查找不到联系人则抛出404异常
     */
    public function findOne($id)
    {
        $model = User::findOne($id);
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('该用户不存在');
        }
    }

    /**
     * 查找一个联系人profile表.
     *
     * @param int $id 联系人ID
     *
     * @return null|static
     *
     * @throws NotFoundHttpException 查找不到联系人则抛出404异常
     */
    public function findProfile($id)
    {
        $model = Profile::find()
                ->where(['user_id' => $id])
                ->one();
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('该用户不存在');
        }
    }// end function
}// end controller

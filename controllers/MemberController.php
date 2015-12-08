<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Profile;
use someet\common\models\User;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 *
 * 联系人控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class MemberController extends BackendController
{
    public $enableCsrfValidation = false;

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
                    'index',
                    'search',
                    'update',
                    'search-by-auth',
                    'fetch-white-list',
                    'fetch-black-list',
                    'fetch-user-list-by-role-name',
                    'update-assignment',
                    'set-user-in-white-list',
                ]
            ],
        ];
    }

    /**
     * 获取白名单列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionFetchWhiteList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $users = User::find()
            ->where(['in_white_list' => User::WHITE_LIST_YES])
            ->with([
                'profile'
            ])
            ->asArray()
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();
        return $users;
    }

    /**
     * 获取用户列表, 根据角色名称
     * @param string $role_name 角色名称
     * @return array 用户列表
     */
    public function actionFetchUserListByRoleName($role_name) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //检查参数
        if (empty($role_name)) {
            return ['msg' => '角色名称不能为空'];
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

        //查询列表
        $users = User::find()
            ->joinWith('assignment')
            ->where([
                'status' => User::STATUS_ACTIVE,
                'auth_assignment.item_name' => $role_name,
            ])
            ->with(['profile'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        //返回列表
        return $users;
    }

    /**
     * 更新用户的角色
     * @param integer $user_id 需要更新的用户ID
     * @param string $role_name 更新的角色名称
     * @param integer $assign_or_not 是赋权还是撤权
     */
    public function actionUpdateAssignment($user_id, $role_name, $assign_or_not) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //参数检查
        if ($user_id<1 || empty($role_name) || !in_array($assign_or_not, [0, 1])) {
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

        //判断是否越权操作了
//        if ($auth->hasChild())

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
     * 设置用户为白名单
     * @param integer $user_id 用户ID
     * @param string $in_white_list 是否是白名单 'true' 和 'false'
     * @return array|bool
     */
    public function actionSetUserInWhiteList($user_id, $in_white_list='true') {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ( User::updateAll(['in_white_list' => $in_white_list == 'true' ? User::WHITE_LIST_YES : User::WHITE_LIST_NO], ['id' => $user_id]) ) {
            return [];
        } else {
            return false;
        }
    }

    /**
     * 联系人列表
     * @param integer $id
     * @param string $scenario 场景
     * @param int $perPage
     * @return array|int|null|\yii\db\ActiveRecord|\yii\db\ActiveRecord[]
     */
    public function actionIndex($id = null, $scenario = null, $perPage = 20)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = User::find()
            ->with(['profile'])
            ->asArray()
            ->where(['status' => User::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_DESC]);
        if ($id) {
            $users = User::find()->where(['id' => $id])->with(['profile', 'assignment'])->asArray()->one();
        } elseif ($scenario == "total") {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage
            ]);

            return $pagination->pageCount;
        } elseif ($scenario == "page") {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage
            ]);

            $users = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }
        return $users;
    }

    /**
     * 搜索用户, 供给活动分配发起人的自动完成功能使用
     * @param string $username 用户名
     * @return array
     */
    public function actionSearch($username) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $users = User::find()
            ->where([
                'status' => User::STATUS_ACTIVE,
            ])
            ->andWhere(
                ['like', 'username', $username]
            )
            ->with(['profile'])
            ->limit(50)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        return $users;
    }

    /**
     * 搜索PMA, 供通知时使用
     * @param string $username 用户名
     * @param string $auth 权限, 是什么用户
     * @return array
     */
    public function actionSearchByAuth($username, $auth = "pma") {
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
     * 创建一个联系人
     * @return null|static
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
     * 更新一个联系人
     * @param integer $id 联系人ID
     * @return null|static
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findOne($id);
        $data = Yii::$app->request->post();
        if (isset($data['email'])) {
            $model->email = $data['email'];
            if (!$model->validate('email')) {
                throw new DataValidationFailedException($model->getFirstError('email'));
            }
        }

        if (isset($data['password']) && $data['password']!='') {
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

        \someet\common\models\AdminLog::saveLog('更新联系人', $model->primaryKey);

        return $this->findOne($id);
    }

    /**
     * 查找一个联系人
     * @param integer $id 联系人ID
     * @return null|static
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

}
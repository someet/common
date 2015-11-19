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


class MemberController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

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
                    'search-principal',
                    'fetch-white-list',
                    'fetch-black-list',
                    'fetch-pma-list',
                    'fetch-founder-list',
                    'set-user-as-pma',
                    'set-user-as-founder',
                    'set-user-in-white-list',
                ]
            ],
        ];
    }

    //白名单列表
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

    //PMA列表
    public function actionFetchPmaList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()
            ->joinWith('assignment')
            ->where([
                'status' => User::STATUS_ACTIVE,
                'auth_assignment.item_name' => 'pma',
            ])
            ->with(['profile'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        return $users;
    }

    //PMA列表
    public function actionFetchFounderList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()
            ->joinWith('assignment')
            ->where([
                'status' => User::STATUS_ACTIVE,
                'auth_assignment.item_name' => 'founder',
            ])
            ->with(['profile'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        return $users;
    }

    // 设置用户为白名单
<<<<<<< HEAD
    public function actionSetUserInWhiteList($user_id, $in_white_list='true') {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ( User::updateAll(['in_white_list' => $in_white_list == 'true' ? User::WHITE_LIST_YES : User::WHITE_LIST_NO], ['id' => $user_id]) ) {
=======
    public function actionSetUserInWhiteList($user_id, $in_white_list=User::WHITE_LIST_YES) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ( User::updateAll(['in_white_list' => $in_white_list], ['id' => $user_id]) ) {
>>>>>>> complete set white list function
            return [];
        } else {
            return false;
        }
    }

    //设置用户为PMA
<<<<<<< HEAD
    public function actionSetUserAsPma($user_id, $assign='true') {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('pma');
        if ($assign == 'true') {
            echo 1;
            $auth->assign($role, $user_id);
        } else {
            echo 2;
            $auth->revoke($role, $user_id);
        }
        return [];
    }

    //设置用户为发起人
    public function actionSetUserAsFounder($user_id, $assign='true') {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('founder');
        if ($assign == 'true') {
            $auth->assign($role, $user_id);
        } else {
            $auth->revoke($role, $user_id);
        }
        return [];
=======
    public function actionSetUserAsPma($user_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ( User::updateAll(['in_white_list' => User::WHITE_LIST_YES], ['id' => $user_id]) ) {
            return [];
        } else {
            return false;
        }

    }

    //设置用户为发起人
    public function actionSetUserAsFounder($user_id) {

>>>>>>> complete set white list function
    }

    public function actionIndex($id = null, $scenario = null, $perPage = 20)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = User::find()
            ->with(['profile'])
            ->asArray()
            ->where(['status' => User::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_DESC]);
        if ($id != null) {
            $users = User::find()->where(['id' => $id])->with(['profile', 'assignment'])->asArray()->one();
        } elseif ($scenario == "total") {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'defaultPageSize' => $perPage
            ]);

            return $pagination->pageCount;
        } elseif ($scenario == "page") {
            $countQuery = clone $query;
            $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'defaultPageSize' => $perPage
            ]);

            $users = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        }
        return $users;
    }

    /**
     * 搜索用户, 供给活动分配发起人的自动完成功能使用
     * @param $username 用户名
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
     * @param $username 用户名
     * @return array
     */
    public function actionSearchPrincipal($username) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $users = User::find()
            ->joinWith('assignment')
            ->where([
                'status' => User::STATUS_ACTIVE,
                'auth_assignment.item_name' => 'pma',
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

        \someet\common\models\AdminLog::saveLog($this->searchById($model->id), $model->primaryKey);

        return $this->findOne($id);
    }

    public function findOne($id)
    {
        $model = User::findOne($id);
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('该用户不存在');
        }
    }

    public function searchById($id){
        if (($model = User::findOne($id)) !== null) {
            return json_encode($model->toArray());
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }
}
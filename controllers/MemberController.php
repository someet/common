<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
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
                ]
            ],
        ];
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
            $users = $this->findOne($id);
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
        $post = Yii::$app->request->post();
        if (isset($post['email'])) {
            $model->email = $post['email'];
//            if(!$model->validation('email')){
//                throw new DataValidationFailedException($model->getFirstError('email'));
//            }
            if (!$model->save()) {
                throw new ServerErrorHttpException();
            }

            return $this->findOne($id);
        }
        if (isset($post['status'])) {
            $model->status = $post['status'];
            if (!$model->save()) {
                throw new ServerErrorHttpException();
            }

            return $this->findOne($id);
        }
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
}
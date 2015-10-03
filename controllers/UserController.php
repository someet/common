<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use app\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;


class UserController extends Controller
{

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
        ];
    }

    public function actionIndex($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = User::find()
            ->orderBy(['id' => SORT_DESC]);
        if ($id != null) {
            $users = $query->where(['id' => $id])
                ->one();
        } else {
            $users = $query->all();
        }

        return $users;
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $user = new User();
        $user->username = $post['username'];
        $user->setPassword($post['password']);
        $user->generateAuthKey();
        $user->email = $post['email'];
        $user->role = User::ROLE_USER;
        $user->save();

    }

    public function actionUpdate($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findOne($id);
        $post = Yii::$app->request->post();
        if(isset($post['email'])){
            $model->email = $post['email'];
//            if(!$model->validation('email')){
//                throw new DataValidationFailedException($model->getFirstError('email'));
//            }
            if(!$model->save()){
                throw new ServerErrorHttpException();
            }

            return $this->findOne($id);
        }
    }

    public function findOne($id){
        $model = User::findOne($id);
        if(isset($model)){
            return $model;
        }else{
            throw new NotFoundHttpException('该用户不存在');
        }
    }
}
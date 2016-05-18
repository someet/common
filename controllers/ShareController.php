<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Share;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\data\Pagination;

/**
 *
 * 联系人控制器
 *
 * @author wangshudong
 * @package app\controllers
 */

class ShareController extends BackendController
{

    /**
    *权限控制
    *
    */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'update' => ['post'],
                    'create' => ['post'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }
    
    /**
    *获取单页数据
    *
    */
    public function actionIndex($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Share::find()
                ->where(['status' => Share::STATUS_ENABLE])
                ->where(['id' => $id])
                ->one();

        return $model;
    }

    /**
    *获取列表列表
    *
    */

    public function actionList()
    {
        //查询出本周的所有列表内容
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Share::find()
                // ->where(['status' => Share::STATUS_ENABLE])
                ->andWhere('created_at >' .getLastEndTime())
                ->all();

        return $model;
    }

    /**
    *创建数据
    *
    */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $user_id = Yii::$app->user->id;
        $data = $request->post();
        $model = new Share;
        $model->created_at = time();
        $model->user_id = $user_id;

        if ($model->load($data, '') && $model->save()) {
            \someet\common\models\AdminLog::saveLog('分享内容', $model->primaryKey);
            return Share::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }

    }

    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user_id = Yii::$app->user->id;

        $model = Share::findOne($id);
        $data = Yii::$app->getRequest()->post();
        $model->user_id = $user_id;
        $model->id = $id;


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

        if (isset($data['link'])) {
            $model->link = $data['link'];
            if (!$model->validate('link')) {
                throw new DataValidationFailedException($model->getFirstError('link'));
            }
        }

        if (isset($data['imgurl'])) {
            $model->imgurl = $data['imgurl'];
            if (!$model->validate('imgurl')) {
                throw new DataValidationFailedException($model->getFirstError('imgurl'));
            }
        }

        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }
        // \someet\common\models\AdminLog::saveLog('更新分享', $model->primaryKey);

        return $model;
        
    }
}

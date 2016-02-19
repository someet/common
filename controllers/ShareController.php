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

class ShareController extends BackendController{


	public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'update' => ['post'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }
	
    public function actionIndex($id = 1){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Share::findOne($id);

        return $model;
    }

	public function actionUpdate($id = 1)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Share::findOne($id);
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

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }
        \someet\common\models\AdminLog::saveLog('更新分享', $model->primaryKey);

        return $model;
		
	}
	
}

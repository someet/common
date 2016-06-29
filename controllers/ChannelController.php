<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\data\Pagination;

/**
 *
 *  频道控制器
 *
 * @author maxwelldu@someet.so
 * @package app\controllers
 */
class ChannelController extends BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'create' => ['get'],
                    'update' => ['post'],
                    'delete' => ['post'],
                    'view' => ['get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
                'allowActions' => [
                    'index',
                    'create',
                ]
            ],
        ];
    }

    /**
     * 查询频道的json数据
     * @return object
     */
    public function actionIndex(){
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Yii::$app->cache->get("channel");
    }

    /**
     * 创建频道的json数据
     * ~~~
     * [{"type_id": "1"},{"type_id": "2"}]
     * ~~~
     * @return object
     */
    public function actionCreate($channel){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!empty($channel)) {
            Yii::$app->cache->set("channel", $channel);
            return Yii::$app->cache->get('channel');
        }
    }
}
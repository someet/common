<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Activity;
use someet\common\models\RActivitySpace;
use someet\common\models\SpaceSection;
use someet\common\models\AdminLog;
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
 *  微信控制器
 *
 * @author wsd312@163.com
 * @package app\controllers
 */
class WechatController extends BackendController
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
     * 查询微信回复的json数据
     * @return object
     */
    public function actionIndex(){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	
    	return Yii::$app->cache->get("wechatReply");
    }    

    /**
     * 创建微信回复的json数据
     * @return object
     */
    public function actionCreate($wechatReply){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	// $wechatReply = '[{"activity_id": "1","order_id":"100"},{"activity_id": "2","order_id": "2001"}]';
    	if (!empty($wechatReply)) {
    		Yii::$app->cache->set("wechatReply",$wechatReply);
    		return Yii::$app->cache->get('wechatReply');
    	}
    }
}
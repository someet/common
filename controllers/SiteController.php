<?php

namespace app\controllers;

use Exception;
use someet\common\models\forms\LoginForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use e96\sentry\SentryHelper;

/**
 *
 * 站点控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class SiteController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
                'allowActions' => [
                    'error',
                    'logout',
                    'fetch'
                ]
            ],
        ];
    }

    /**
     * 站点首页
     */
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    /**
     * 用户退出
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}

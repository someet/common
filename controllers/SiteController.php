<?php

namespace app\controllers;

use Exception;
use someet\common\models\forms\LoginForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use e96\sentry\SentryHelper;

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
                    'test-sentry',
                    'fetch'
                ]
            ],
        ];
    }

    /**
     * Render the homepage
     */
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }

    /**
     * 测试sentry
     */
    public function actionTestSentry()
    {
        try {
            throw new Exception('FAIL');
        } catch (Exception $e) {
            SentryHelper::captureWithMessage('Fail to save model', $e);
        }
    }

    /**
     * User logout
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}

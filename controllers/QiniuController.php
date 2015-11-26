<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 *
 * 七牛控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class QiniuController extends BackendController
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
                    'get-upload-token' => ['get'],
                    'create-completely-url' => ['post'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }

    /**
     * 创建一个完整的URL
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCreateCompletelyUrl()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $key = Yii::$app->request->post('key');
        if ($key === null) {
            throw new BadRequestHttpException('key can not be blank');
        }

        /* @var \app\components\QiniuComponent $qiniu */
        $qiniu = Yii::$app->qiniu;
        $url = $qiniu->completelyUrl($key);

        return [
            'url' => $url,
        ];
    }

    /**
     * 获取上传的TOKEN
     * @return array
     */
    public function actionGetUploadToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $bucket = Yii::$app->params['qiniu.bucket'];
        $expires = Yii::$app->params['qiniu.upload_token_expires'];

        /* @var \app\components\QiniuComponent $qiniu */
        $qiniu = Yii::$app->qiniu;
        $token = $qiniu->getUploadToken($bucket, null, $expires);

        return [
            'token' => $token,
            'bucket' => $bucket,
            'expires' => $expires,
        ];
    }
}

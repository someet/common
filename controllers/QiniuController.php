<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class QiniuController extends Controller
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
        ];
    }

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

<?php
/**
 * Created by PhpStorm.
 * User: michaeldu
 * Date: 15/11/9
 * Time: 下午4:30
 */

namespace app\controllers;

use Exception;
use someet\common\models\AdminLog;
use Yii;
use yii\web\Controller;
use e96\sentry\SentryHelper;

class BackendController extends Controller
{
    /**
     * 保存系统日志
     * @param $data adminLog的数组
     */
    protected function SaveAdminLog($data)
    {
        try {
            $model = new AdminLog();
            $model->load($data, '') && $model->save();
        } catch (Exception $e) {
            SentryHelper::captureWithMessage('后台日志记录失败', $e);
        }
    }

}
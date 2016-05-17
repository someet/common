<?php
namespace app\controllers;

use Exception;
use someet\common\models\AdminLog;
use Yii;
use yii\web\Controller;
use e96\sentry\SentryHelper;

/**
 *
 * 后台通用控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class BackendController extends Controller
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
}

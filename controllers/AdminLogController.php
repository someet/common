<?php
/**
 * Created by PhpStorm.
 * User: michaeldu
 * Date: 15/11/9
 * Time: 下午6:21
 */

namespace app\controllers;


use someet\common\models\AdminLog;
use yii\data\ActiveDataProvider;

/**
 *
 * 后台管理日志控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class AdminLogController extends BackendController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AdminLog::find(),
            'sort' => [
                'defaultOrder' => [
                    'addtime' => SORT_DESC
                ]
            ],
        ]);
        return $this->render('index',[
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 查看单个后台管理日志
     * @param integer $id 操作日志记录id
     * @return string
     */
    public function actionView($id){
        return $this->render('view',[
            'model'=>AdminLog::findOne($id),
        ]);
    }
}
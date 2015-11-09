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

class AdminLogController extends \yii\web\Controller
{
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

    public function actionView($id){
        return $this->render('view',[
            'model'=>AdminLog::findOne($id),
        ]);
    }
}
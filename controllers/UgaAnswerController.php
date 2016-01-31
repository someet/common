<?php

namespace app\controllers;
use yii\web\Response;
use Yii;
use someet\common\models\UgaQuestion;
use someet\common\models\UgaAnswer;
/*
*uga回答系统控制器
*
*/
class UgaAnswerController extends \yii\web\Controller
{
/**
     * 查询出关于一个问题的所有答案
     */
    public function actionList($question_id)
    {

    	Yii::$app->response->format = Response::FORMAT_JSON;

        //查询答案列表，带上被赞的数量
        $answerList = UgaQuestion::find()
        			->with(['answerList','answerList.user','answerList.user.profile'])
        			->where(['id'=>$question_id])
        			->andWhere(['status'=>UgaQuestion::STATUS_NORMAL])
        			->asArray()
        			->one();

        return $answerList;
    }

    /**
     * 审核答案
     * @param int $id 1|0 删除|恢复
     */
    public function actionDelete($id,$status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //状态 = $delete ? 0 : 1;
        //更新答案的状态
        if (empty($id)) {

        	return 'id不存在';
        }

        $model = UgaAnswer::findOne($id);
      	if(UgaAnswer::STATUS_DELETED == $status ){
        	$model->status = UgaAnswer::STATUS_DELETED;
      	}elseif (UgaAnswer::STATUS_NORMAL == $status) {
        	$model->status = UgaAnswer::STATUS_DELETED;
      	}
        if ($model->save() === false) {
            throw new ServerErrorHttpException('删除失败');
        }
        \someet\common\models\AdminLog::saveLog('删除活动', $model->primaryKey);

        return [];
    
    }

}

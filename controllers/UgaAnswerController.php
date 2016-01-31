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
        $answerList = UgaAnswer::find()
        			->where(['question_id'=>$question_id])
        			->all();

        return $answerList;
    }

    /**
     * 审核答案
     * @param int $delete 1|0 删除|恢复
     */
    public function actionReview($delete)
    {
        //状态 = $delete ? 0 : 1;
        //更新答案的状态
    }

}

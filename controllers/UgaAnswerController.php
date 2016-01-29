<?php

namespace app\controllers;
/*
*uga回答系统控制器
*
*/
class UgaAnswerController extends \yii\web\Controller
{
/**
     * 查询出关于一个问题的所有答案
     */
    public function actionList()
    {
        //查询答案列表，带上被赞的数量
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

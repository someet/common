<?php

namespace app\controllers;
use yii\web\Response;
use Yii;
use someet\common\models\UgaQuestion;
use someet\common\models\UgaAnswer;
use yii\data\Pagination;
/*
*uga回答系统控制器
*
*/
class UgaAnswerController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['post'],
                    'view' => ['get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }

	/**
	* @param $question_id 问题id
	* @param $scenario 判断是否分页
	* @param $perPage 分页的size
	* @param $order 排序方式
	* @param $question_id 问题id
    * 查询出关于一个问题的所有答案
    */
    public function actionList($question_id, $scenario = null, $perPage = 20, $order = 'id')
    {

    	Yii::$app->response->format = Response::FORMAT_JSON;

    	if (empty($question_id)) {
    		return '参数不正确';
    	}

    	$question = UgaQuestion::findOne($question_id);

        //查询答案列表，带上被赞的数量
        $answerList = UgaAnswer::find()
        			->with(['question','user','user.profile'])
        			->where(['question_id'=>$question_id])
                    ->orderBy([$order => SORT_DESC])
        			->asArray();

    	$answerLists = '';
    	
		if ($scenario == "total") {
            $countAnswerList = clone $answerList;
            $pagination = new Pagination([
                    'totalCount' => $countAnswerList->count(),
                    'pageSize' => $perPage
                ]);
            return $pagination->totalCount;

        } elseif ($scenario == "page") {
            $countAnswerList = clone $answerList;
            $pagination = new Pagination([
                'totalCount' => $countAnswerList->count(),
                'pageSize' => $perPage
            ]);

            $answerLists = $countAnswerList->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

        }
        $result = ['question' => $question,'answerLists' => $answerLists];
        return $result;
    }

    /**
     * 审核答案
     * @param int $id 
     * @param int $status 1|0 删除|恢复
     */
    public function actionDelete($id,$status)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($id) || empty($status)) {
        	return '参数不存在';
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

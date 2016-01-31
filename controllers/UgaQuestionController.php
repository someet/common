<?php

namespace app\controllers;
use yii\web\Response;
use Yii;
use someet\common\models\UgaQuestion;
use yii\data\Pagination;

/**
*	Uga问题系统控制器
*
*/
class UgaQuestionController extends \yii\web\Controller
{
	/**
     * 首页获取问题的项
     */
    public function actionFetch()
    {
        //问题总数
        //官方问题
        //民间问题 = 问题总数 - 官方总数

        //回答总数
        //官方回答
        //民间回答 = 回答总数 - 官方回答

        //查询官方问题回答数量最多的10条
        //查看民间问题回答数量最多的10条
    }

    /**
     * 获取所有的问题列表
     * @param int $order ups|times 按赞|间
     */
    public function actionList($id = null, $is_official=10,$order = 'id', $perPage = 20, $scenario = null)
    {

    	Yii::$app->response->format = Response::FORMAT_JSON;
        //分页查询所有的问题，两种排序方式，按时间或者按赞降序
        if ( 10 == $is_official || 0 == $is_official || 2 == $is_official || 1 == $is_official) {
            
            //官方问题
            $officialQuestion = UgaQuestion::find()
            			->with(['answerList'])
                        ->where(['is_official'=>$is_official])
                        ->orderBy([$order => SORT_DESC])
            			->asArray();
            			// ->all();


            // print_r($officialQuestions);
            if ($id) {
                $officialQuestion = UgaQuestion::find()
                            ->with(['answerList'])
                            ->where(['id' => $id])
                            ->andWhere(['is_official'=>$is_official])
                            ->orderBy([$order => SORT_DESC])
                            ->asArray()
                            ->one();


            } elseif ($scenario == "total") {

                $countOfficialQuestion = clone $officialQuestion;
                $pagination = new Pagination([
                        'totalCount' => $countOfficialQuestion->count(),
                        'pageSize' => $perPage
                    ]);
                return $pagination->totalCount;

            } elseif ($scenario == "page") {
                $countOfficialQuestion = clone $officialQuestion;
                $pagination = new Pagination([
                    'totalCount' => $countOfficialQuestion->count(),
                    'pageSize' => $perPage
                ]);

                $officialQuestions = $officialQuestion->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();
                foreach($officialQuestions as $key => $officialQuestion) {
                    $officialQuestions[$key]['preview_url'] = Yii::$app->params['domain'].'preview/'.$officialQuestion['id'];
                    $officialQuestions[$key]['filter_url'] = Yii::$app->params['domain'].'filter/'.$officialQuestion['id'];

                }

                return $officialQuestions;
            }



        }else {
            return '参数不正确';
        }
    }

    /**
     * 审核问题
     * @param int $delete 1|0 删除|恢复
     */
    public function actionReview($delete)
    {
        //状态 = $delete ? 0 : 1;
        //更新问题的状态
    }

    /**
     * 放入公共库
     * @param int $open 1|0 公共库|私有库
     */
    public function actionPublic($open)
    {
        //状态 = $open ? 1 : 0;
        //更新问题的状态
    }

}

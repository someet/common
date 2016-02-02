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
     * 添加一个Uga问题
     * @return mixed
     * @throws DataValidationFailedException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        $data = $request->post();
        $model = new UgaQuestion();

        if ($model->load($data, '') && $model->save()) {
            // 保存操作记录
            \someet\common\models\AdminLog::saveLog('添加Uga问题', $model->primaryKey);
            return UgaQuestion::findOne($model->id);
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }
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
    public function actionReview($id,$status)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($id) && empty($status)) {
            return '参数不正确';
        }
        
        $model = UgaQuestion::findOne($id);

        if(UgaQuestion::STATUS_DELETED == $status ){
            $model->status = UgaQuestion::STATUS_DELETED;
        }elseif (UgaQuestion::STATUS_NORMAL == $status) {
            $model->status = UgaQuestion::STATUS_NORMAL;
        }
        if ($model->save() === false) {
            throw new ServerErrorHttpException('删除失败');
        }
        \someet\common\models\AdminLog::saveLog('删除活动', $model->primaryKey);

        return [];
    }

    /**
     * 放入公共库
     * @param int $open 1|0 公共库|私有库
     */
    public function actionPublic($id,$open)
    {
        //状态 = $open ? 1 : 0;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($id) && empty($open)) {
            return '参数不存在';
        }

        $model = UgaQuestion::findOne($id);

        if(UgaQuestion::FOLK_PUBLICK == $open ){
            $model->is_official = UgaQuestion::FOLK_PUBLICK;
        }elseif (UgaQuestion::FOLK_PRIVATE == $open) {
            $model->is_official = UgaQuestion::FOLK_PRIVATE;
        }
        if ($model->save() === false) {
            throw new ServerErrorHttpException('放入公共库');
        }
        \someet\common\models\AdminLog::saveLog('移除公共库', $model->primaryKey);

        return [];
    }

}

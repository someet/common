<?php

namespace app\controllers;
use someet\common\models\UgaAnswer;
use yii\filters\VerbFilter;
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
    public function actionData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        //问题总数
        $questions = UgaQuestion::find()->count();

        //官方问题
        $officialQuestions = UgaQuestion::find()->where(['is_official' => UgaQuestion::OFFICIAL_IS])->count();

        //民间问题 = 问题总数 - 官方总数
        $notOfficialQuestions = $questions - $officialQuestions;

        //回答总数
        $answers = UgaAnswer::find()->count();

        //官方回答
        //民间回答 = 回答总数 - 官方回答

        //查询官方问题回答数量最多的10条
        $officialQuestionTop = UgaQuestion::find()->where(['is_official' => UgaQuestion::OFFICIAL_IS])->orderBy(['answer_num' => SORT_DESC])->limit(10)->all();

        //查看民间问题回答数量最多的10条
        $notOfficialQuestionTop = UgaQuestion::find()->where(['is_official' => UgaQuestion::OFFICIAL_NO])->orderBy(['answer_num' => SORT_DESC])->limit(10)->all();

        return [
            'questions' => $questions,
            'officialQuestions' => $officialQuestions,
            'notOfficialQuestions' => $notOfficialQuestions,
            'answers' => $answers,
            'officialQuestionTop' => $officialQuestionTop,
            'notOfficialQuestionTop' => $notOfficialQuestionTop
        ];

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
     * 更新
     * @param $id
     * @return mixed
     * @throws DataValidationFailedException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $data = Yii::$app->getRequest()->post();

        if (isset($data['content'])) {
            $model->content = $data['content'];
            if (!$model->validate('content')) {
                throw new DataValidationFailedException($model->getFirstError('content'));
            }
        }

        if (isset($data['is_official'])) {
            $model->is_official = $data['is_official'];
            if (!$model->validate('is_official')) {
                throw new DataValidationFailedException($model->getFirstError('is_official'));
            }
        }

        if (isset($data['status'])) {
            $model->status = $data['status'];
            if (!$model->validate('status')) {
                throw new DataValidationFailedException($model->getFirstError('status'));
            }
        }

        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }

        return $this->findModel($id);
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


    /**
     * 查找Uga问题
     * @param integer $id 问题ID
     * @return Activity 问题对象
     * @throws NotFoundHttpException 如果没有查找到则抛出404异常
     */
    public function findModel($id)
    {
        $model = UgaQuestion::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("Uga问题不存在");
        }
    }

}

<?php

namespace app\controllers;

use app\components\DataValidationFailedException;
use someet\common\models\Question;
use someet\common\models\QuestionItem;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class QuestionController extends Controller
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

    /**
     * @inheritdoc
     */
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
                    'viewByActivityId' => ['get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
                'allowActions' => [
                    'view-by-activity-id',
                ]
            ],
        ];
    }

    /**
     * 添加一个问题, 包括主表和问题项
     *
     * POST 请求 /question/create
     *
     * ~~~
     * {
     *   "questionItemList": {
     *      <string: 问题1>,
     *    }
     * }
     * ~~~
     *
     * @return  array
     *
     * 失败
     *
     * ~~~
     * {
     * "success": "0",
     * "errmsg": "名称长度不得超过255个字符",
     * "status_code": 422
     * }
     * ~~~
     *
     * 成功
     *
     * {
     * "success": "1",
     * "data": {
     *   "id": 10,
     *   "status": 10
     * },
     * "status_code": 200
     * }
     *
     * @throws DataValidationFailedException
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        $data = $request->post();
        $questionItemList = $data['questionItemList'];
        $model = new Question;

        $transaction = $model->getDb()->beginTransaction();
        $flag = true;
        if ($model->load($data, '') && $model->save()) {
            $question_id = $model->id;
            if (!empty($questionItemList)) {
                foreach ($questionItemList as $questionItem) {
                    $questionItemModel = new QuestionItem();
                    $questionItemModel->question_id = $question_id;
                    if ($questionItemModel->load($questionItem, '') && $questionItemModel->save()) {

                    } elseif ($questionItemModel->hasErrors()) {
                        $flag = false;
                        $errors = $model->getFirstErrors();
                        //throw new DataValidationFailedException(array_pop($errors));
                    } else {
                        throw new ServerErrorHttpException();
                    }
                }
            }

            if ($flag) {
                $transaction->commit();
                \someet\common\models\AdminLog::saveLog($this->searchById($model->id), $model->primaryKey);
                return Question::find()
                    ->where(['id' => $model->id])
                    ->asArray()
                    ->with('questionItemList')
                    ->one();
            } else {
                $transaction->rollBack();
                throw new DataValidationFailedException('设置问题异常');
            }
        } elseif ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            throw new DataValidationFailedException(array_pop($errors));
        } else {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * 修改 问题
     *
     * POST 提交到 /question/update?id=10
     *
     * ~~~
     * {
     *   "name": "户外1",
     * }
     * ~~~
     *
     *
     * @param $id
     * @return array
     *
     * 成功
     *
     * ~~~
     * {
     *   "success": "1",
     *   "data": {
     *     "id": 10,
     *     "name": "户外1",
     *     "status": 10
     *   },
     *   "status_code": 200
     * }
     * ~~~
     *
     * 失败
     *
     * ~~~
     * {
     *   "success": "0",
     *   "errmsg": "名称最少含有2个字符",
     *   "status_code": 422
     * }
     * ~~~
     *
     * @throws DataValidationFailedException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $data = Yii::$app->getRequest()->post();
        $questionItemList = isset($data['questionItemList']) ? $data['questionItemList'] : [];

        if (isset($data['title'])) {
            $model->title = $data['title'];
            if (!$model->validate('title')) {
                throw new DataValidationFailedException($model->getFirstError('title'));
            }
        }

        if (isset($data['desc'])) {
            $model->desc = $data['desc'];
            if (!$model->validate('desc')) {
                throw new DataValidationFailedException($model->getFirstError('desc'));
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

        if (!empty($questionItemList)) {
            foreach ($questionItemList as $questionItem) {
                $questionItemModel = QuestionItem::findOne($questionItem['id']);
                $questionItemModel->label = $questionItem['label'];
                if ($questionItemModel->save()) {
                } elseif ($questionItemModel->hasErrors()) {
                    $errors = $model->getFirstErrors();
                    throw new DataValidationFailedException(array_pop($errors));
                } else {
                    throw new ServerErrorHttpException();
                }
            }
        }

        \someet\common\models\AdminLog::saveLog($this->searchById($model->id), $model->primaryKey);
        return Question::find()
            ->where(['id' => $model->id])
            ->asArray()
            ->with('questionItemList')
            ->one();
    }

    /**
     * 删除问题
     * POST 请求 /question/delete?id=10
     *
     * @param $id
     * @return array
     *
     * 成功
     *
     * ~~~
     * {
     *   "success": "1",
     *   "data": [],
     *   "status_code": 200
     * }
     * ~~~
     *
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if ($model->delete() === false) {
            throw new ServerErrorHttpException('删除失败');
        }

        return [];
    }

    public function actionView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Question::find()
            ->where(['id' => $id])
            ->asArray()
            ->with('questionItemList')
            ->one();

        return $model;
    }


    public function actionViewByActivityId($activity_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Question::find()
            ->where(['activity_id' => $activity_id])
            ->with('questionItemList')
            ->asArray()
            ->one();

        return $model;
    }

    /**
     * @param $id
     * @return Question
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Question::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("问题不存在");
        }
    }

    public function searchById($id){
        if (($model = Question::findOne($id)) !== null) {
            return json_encode($model->toArray());
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }
}

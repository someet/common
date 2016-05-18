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

/**
 *
 * 表单控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class QuestionController extends BackendController
{

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

        //验证三个问题不能为空
        if (!isset($data['questionItemList'])) {
            Yii::error('表单的三个问题不能为空');
            return ['msg' => '三个问题不能为空'];
        }

        //验证必须是三个问题
        $questionItemList = $data['questionItemList'];
        if (count($questionItemList)!=3) {
            Yii::error('请设置三个问题');
            return ['msg' => '请设置三个问题'];
        }

        $model = new Question;

        //开启事务
        $transaction = $model->getDb()->beginTransaction();

        //问题标记
        $questionFlag = true;

        //尝试保存问题主记录
        if ($model->load($data, '') && $model->save()) {
            $question_id = $model->id;

            //遍历三个问题
            foreach ($questionItemList as $questionItem) {
                $questionItemModel = new QuestionItem();
                $questionItemModel->question_id = $question_id;

                //尝试保存问题项
                if (!$questionItemModel->load($questionItem, '') || !$questionItemModel->save()) {
                    $questionFlag = false;
                    Yii::error('设置问题项出错');
                }
            }

            //判断是否有错误
            if ($questionFlag) {
                //提交事务
                $transaction->commit();

                //记录后台操作记录日志
                \someet\common\models\AdminLog::saveLog('添加表单', $model->primaryKey);

                //返回问题对象
                return Question::find()
                    ->where(['id' => $model->id])
                    ->asArray()
                    ->with('questionItemList')
                    ->one();
            }
        }

        //回滚事务
        $transaction->rollBack();
        Yii::error('设置问题出错');
        return ['msg' => '设置问题出错'];
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

        \someet\common\models\AdminLog::saveLog('更新表单', $model->primaryKey);
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

    /**
     * 查看一个表单
     * @param integer $id 表单ID
     * @return array|null|\yii\db\ActiveRecord
     */
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

    /**
     * 根据活动ID查看表单
     * @param integer $activity_id 活动ID
     * @return array|null|\yii\db\ActiveRecord
     */
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
     * 查找表单
     * @param integer $id 表单ID
     * @return Question 表单对象
     * @throws NotFoundHttpException 如果查找不到则抛出404异常
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
}

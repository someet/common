<?php
namespace someet\common\services;

use someet\common\models\Activity;
use someet\common\models\QuestionItem;
use someet\common\models\AnswerItem;
use someet\common\models\Answer;
use someet\common\models\User;
use Yii;

class AnswerService extends \someet\common\models\Answer
{
    use ServiceError;

    /**
     * 报名
     *
     * @param int $question_id 问题ID
     * @param int $activity_id 活动ID
     * @param array $post 提交的答案 For example:
     * {
     *      "question_id":"2",
     *      "activity_id":"78",
     *      "q1":{
     *          "question_item_id":"76",
     *          "question_value":"haha"
     *      },
     *      "q2":{
     *          "question_item_id":"77",
     *          "question_value":"hha"
     *      },
     *      "q3":{
     *          "question_item_id":"78",
     *          "question_value":"haa"
     *      }
     * }
     * @return array
     * @throws \yii\db\Exception
     */
    public function join($question_id, $activity_id, $post)
    {
        $user_id = Yii::$app->user->id;

        if (!is_array($post['q1']) || !is_array($post['q2']) || !is_array($post['q3'])) {
            $this->setError('三个问题不完整');
            return false;
        }

        $activity = Activity::findOne($activity_id);
        if (!$activity) {
            $this->setError('活动不存在');
            return false;
        }

        if ($activity->status == Activity::STATUS_SHUT) {
            $this->setError('当前活动已关闭');
            return false;
        }

        if ($activity->join_people_count >= $activity->peoples) {
            $this->setError('活动已报满,无法报名');
            return false;
        }

        if (Answer::find()->where(['question_id' => $question_id, 'user_id' => $user_id])->exists()) {
            $this->setError('无法重新报名');
            return false;
        }

        $user = User::findOne($user_id);

        //获取问题列表
        $questionItemList = QuestionItem::findAll(['question_id' => $question_id]);
        if (3 != count($questionItemList)) {
            $this->setError('问题不是三个');
            return false;
        }

        // 组装answerlist
        $answerItemList = [
            'q1' => [
                'question_item_id' => $post['q1']['question_item_id'],
                'question_value' => $post['q1']['question_value'],
                'question_label'=>$questionItemList['0']['label']
            ],
            'q2' => [
                'question_item_id' => $post['q2']['question_item_id'],
                'question_value' => $post['q2']['question_value'],
                'question_label'=>$questionItemList['1']['label']
            ],
            'q3' => [
                'question_item_id' => $post['q3']['question_item_id'],
                'question_value' => $post['q3']['question_value'],
                'question_label'=>$questionItemList['2']['label']
            ],
        ];

        $model = new Answer();
        $transaction = $model->getDb()->beginTransaction();

        $data = ['question_id' => $question_id, 'activity_id' => $activity_id, 'status' => Answer::STATUS_REVIEW_YET];
        $model->load($data, '');
        if (!$model->save()) {
            $transaction->rollBack();
            $this->setError('答案保存失败');
            return false;
        }

        foreach ($answerItemList as $answer) {
            $answerModel = new AnswerItem();
            $answerModel->question_id = $question_id;
            if (!$answerModel->load($answer, '') || !$answerModel->save()) {
                $transaction->rollBack();
                $this->setError('答案项保存失败');
                return false;
            }
        }

        if (0 == $user->updateCounters(['join_count' => 1])) {
            $transaction->rollBack();
            $this->setError('更新用户参加次数失败');
            return false;
        }

        if (0 == $activity->updateCounters(['join_people_count' => 1])) {
            $transaction->rollBack();
            $this->setError('更新活动参加的人数失败');
            return false;
        }

        //查询现在的活动人数是否已经报满
        $join_people_count = $activity->join_people_count;
        $is_full = $join_people_count < $activity->peoples ? Activity::IS_FULL_NO : Activity::IS_FULL_YES;

        //如果 is_full 和之前的值一样则无需要更新
        if ($is_full != $activity->is_full) {
            //尝试更新活动是否已报名完成字段, updateAll 返回受影响的行数,如果修改成功一条则返回1, 如果修改失败则标识报名失败
            if (0 == $activity->updateAll(['is_full' => $is_full])) {
                $transaction->rollBack();
                $this->setError('更新活动是否报满失败');
                return false;
            }
        }

        $transaction->commit();
        return true;
    }


    /**
     * 报名状态修改
     *
     * @param int $id 报名的ID
     * @param int $status_arrive 0|1|2 到达的状态
     * @return array|null|\yii\db\ActiveRecord
     */
    public function updateArriveStatus($id, $status_arrive)
    {
        // 参数验证
        if ($id < 1 || !in_array($status_arrive, [Answer::STATUS_ARRIVE_ON_TIME, Answer::STATUS_ARRIVE_LATE, Answer::STATUS_ARRIVE_YET])) {
            $this->setError('参数不正确');
            return false;
        }

        $answer = Answer::find()
            ->where(['id' => $id])
            ->with(['user', 'activity'])
            ->one();

        if (!$answer) {
            $this->setError('该报名信息不存在');
            return false;
        }

        $answer->arrive_status = $status_arrive;
        if (!$answer->save()) {
            $this->setError('更新失败');
            return false;
        }

        return true;
    }
}
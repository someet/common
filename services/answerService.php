<?php
namespace someet\common\services;

class AnswerService extends Answer
{
   /**
     * 报名活动添加答案
     *
     * @param  integer $user_id 活动编号
     * @return ActiveDataProvider
     */
    public function actionActivityAnswer()
    {
        $request = Yii::$app->request;
        $result = $request->post();
        $question_id = $request->post('question_id', null);
        $activity_id = $request->post('activity_id', null);
        $user_id = $request->post('user_id', null);

        //验证question_id和activity_id必填
        if (in_array(null, [$question_id, $activity_id])) {
            return ['msg' => '缺少参数'];
        }

        //检查是否已经报过名
        if (Answer::find()->where(['question_id' => $question_id, 'user_id' => $user_id])->exists()) {
            return ['msg' => '无法重复报名'];
        }

        //检查用户还有没有报名次数
        $user = \someet\common\models\User::findOne($user_id);

        //获取问题列表
        $questionItemList = QuestionItem::findAll(['question_id' => $question_id]);
        // 组装answerlist
        $answerList = [];
        
        $answerItemList = [
                'q1' => [
                    'question_item_id' => $result['q1']['question_item_id'],
                    'question_value' => $result['q1']['question_value'],
                    'question_label'=>$questionItemList['0']['label']
                    ],
                'q2' => [
                    'question_item_id' => $result['q2']['question_item_id'],
                    'question_value' => $result['q2']['question_value'],
                    'question_label'=>$questionItemList['1']['label']
                    ],
                'q3' => [
                    'question_item_id' => $result['q3']['question_item_id'],
                    'question_value' => $result['q3']['question_value'],
                    'question_label'=>$questionItemList['2']['label']
                    ],
        ];

        //创建主答案对象
        $model = new Answer();
        //标记报名成功
        $answerFlag = true;
        //开启事务
        $transaction = $model->getDb()->beginTransaction();

        //尝试保存答案主对象
        $data = ['question_id' => $question_id, 'activity_id' => $activity_id];
        $model->status = Answer::STATUS_REVIEW_YET;
        $model->load($data, '');
        $model->save();
        foreach ($answerItemList as $answer) {
                //创建答案项
                $answerModel = new AnswerItem();
                $answerModel->question_item_id = $answer['question_item_id'];
                $answerModel->question_value = $answer['question_value'];
                $answerModel->question_id = $question_id;
                $answerModel->question_label = $answer['question_label'];
                $answerModel->save();
                //尝试保存答案项
            if ($answerModel->load($answer, '') && $answerModel->save()) { //
            } else {
                $answerFlag = false;
            }
        }
            
            //更新用户的添加次数
        if ($answerFlag && 0 == $user->updateCounters(['join_count' => +1])) {
            $answerFlag = false;
        }

            //尝试更新允许报名的次数
        if ($answerFlag && 0 == $user->updateCounters(['allow_join_times' => -1])) {
            $answerFlag = false;
        }

            //尝试更新活动的已报名人数
            $activity = Activity::findOne($activity_id);
        if ($answerFlag && 0 == Activity::updateAllCounters(['join_people_count' => +1], ['id' => $activity_id])) {
            $answerFlag = false;
        }

            // //查询现在的活动人数是否已经报满
            $join_people_count = Answer::find()
                        ->where(['activity_id' => $activity_id ])
                        ->count();
            $is_full = $join_people_count < $activity->peoples ? Activity::IS_FULL_NO : Activity::IS_FULL_YES;

            //如果 is_full 和之前的值一样则无需要更新
        if ($is_full != $activity->is_full) {
            //尝试更新活动是否已报名完成字段, updateAll 返回受影响的行数,如果修改成功一条则返回1, 如果修改失败则标识报名失败
            if ($answerFlag &&  0 == Activity::updateAll(['is_full' => $is_full], ['id' => $activity_id])) {
                $answerFlag = false;
            }
        }
            //如果报名成功
        if ($answerFlag) {
            //提交事务
            $transaction->commit();
            return ['报名成功'];
        }

        //事务回滚
        $transaction->rollBack();
        return ['msg'=>'报名失败，请重新报名'];
        // }
    }
}
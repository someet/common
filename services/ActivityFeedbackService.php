<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 6:03 PM
 */

namespace someet\common\services;

use someet\common\models\Answer;
use Yii;
use someet\common\models\ActivityFeedback;
use yii\db\ActiveQuery;

class ActivityFeedbackService extends BaseService
{

    /**
     * 活动反馈
     *
     * @param array $data POST提交的数据
     * @return array
     * @throws DataValidationFailedException
     */
    public function feedback($data)
    {
        if (!is_array($data)) {
            $this->setError('参数需要是数组');
            return false;
        }

        $user_id = Yii::$app->user->id;
        //检查是否重复提交
        $exists = ActivityFeedback::find()
            ->where(
                [
                    'user_id' => $user_id,
                    'activity_id' => $data['activity_id']
                ]
            )
            ->exists();
        if ($exists) {
            $this->setError('已经反馈过');
            return false;
        }

        $feedback = new ActivityFeedback();
        if ($feedback->load($data, '') && $feedback->save()) {
            Answer::updateAll(['is_feedback' => Answer::FEEDBACK_IS], ['activity_id' => $feedback->activity_id ,'user_id' => $user_id]);
            return Answer::find()
                ->select(['id', 'question_id', 'activity_id', 'user_id'])
                ->where(['id' => $model->id])
                ->with([
                    'user' => function(ActiveQuery $query) {
                        $query->select(['id', 'username', 'mobile', 'wechat_id']);
                    },
                    'answerItemList' => function(ActiveQuery $query) {
                        $query->select(['id', 'user_id', 'question_item_id', 'question_id', 'question_label', 'question_value']);
                    }
                ])
                ->asArray()
                ->one();
        } elseif ($feedback->hasErrors()) {
            $errors = $feedback->getFirstErrors();
            $this->setError(array_pop($errors));
            return false;
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 6:03 PM
 */

namespace someet\common\services;

use someet\common\models\ActivityFeedback;

class ActivityFeedbackService extends ActivityFeedback
{
    use  ServiceError;

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

        //检查是否重复提交
        $exists = ActivityFeedback::find()
            ->where(
                [
                    'user_id' => $data['user_id'],
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
            Answer::updateAll(['is_feedback' => Answer::FEEDBACK_IS], ['activity_id' => $feedback->activity_id ,'user_id' => $feedback->user_id]);
            return true;
        } elseif ($feedback->hasErrors()) {
            $errors = $feedback->getFirstErrors();
            $this->setError(array_pop($errors));
            return false;
        }
    }
}

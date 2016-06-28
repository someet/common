<?php
namespace someet\common\services;

// use dektrium\user\models\Account;
// use app\components\DataValidationFailedException;
// use app\components\ObjectNotExistsException;
// use someet\common\models\Activity;
// use someet\common\models\ActivityFeedback;
// use someet\common\models\ActivityType;
use someet\common\models\Answer;
// use someet\common\models\Profile;
use someet\common\models\Activity;
// use someet\common\models\RActivityFounder;
use Yii;

class ActivityService  //extends \someet\common\models\Activity
{
	/**
	 * 更新报名率
	 * @return 
	 */
	public static function updateRepalyRate($activity_id)
	{
        
        $activity = Activity::findOne($activity_id);

        //报名数量
        $answerNum = Answer::find()
                ->where([
                    'activity_id' => $activity_id,
                    ])
                ->count();

        // 取消报名
        $cancelApplyNum = Answer::find()
                ->where([
                    'activity_id' => $activity_id, 
                    'apply_status' => Answer::APPLY_STATUS_YET,
                    ])
                ->count();        

        // 请假人数
        $leaveNum = Answer::find()
                ->where([
                    'activity_id' => $activity_id, 
                    'leave_status' => Answer::STATUS_LEAVE_YES,
                    ])
                ->count();        

        // 拒绝报名人数
        $rejectNum = Answer::find()
                ->where([
                    'activity_id' => $activity_id, 
                    'status' => Answer::STATUS_REVIEW_REJECT,
                    ])
                ->count();

        if ($activity->ideal_number > 0) {
            $apply_rate = ( $answerNum - $cancelApplyNum - $rejectNum - $leaveNum ) /  $activity->ideal_number;
            $apply_rate = round($apply_rate,2) * 100;
            Activity::updateAll(['apply_rate' => $apply_rate],['id' => $activity_id]);
            return $apply_rate;
        }



	}

}
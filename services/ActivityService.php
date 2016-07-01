<?php
namespace someet\common\services;

use someet\common\models\Answer;
use someet\common\models\Activity;
use Yii;

class ActivityService  extends BaseService
{
	/**
	 * 更新报名率 
     * @param $activity_id 活动id
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
        }

	}


    /**
     * 更新活动的状态
     *
     * @param int $id 活动ID
     * @param int $status 10 草稿 15预发布 20 发布 30 关闭 0 删除
     * @return array
     */
    public function updateStatus($id, $status)
    {
        if (!in_array($status, [Activity::STATUS_RELEASE, Activity::STATUS_DELETE, Activity::STATUS_DRAFT, Activity::STATUS_PREVENT, Activity::STATUS_SHUT])) {
            $this->setError('参数不匹配');
            return false;
        }

        $activity = Activity::findOne($id);
        if (!$activity) {
            $this->setError('活动不存在, ID' . $id);
            return false;
        }

        if ($status == $activity->status) {
            return true;
        }

        $activity->status = $status;
        if (!$activity->save()) {
            $this->setError('更新失败');
            return false;
        }

        return true;
    }

    /**
     * 删除活动
     *
     * @param int $id 活动ID
     * @return array
     */
    public function deleteActivity($id)
    {
        $model = $this->findModel($id);
        if (!$model) {
            $this->setError('活动不存在, ID: ' . $id);
            return false;
        }
        if (Activity::STATUS_DELETE == $model->status) {
            return true;
        }

        $model->status = Activity::STATUS_DELETE;
        if (!$model->save()) {
            $this->setError('删除失败');
            return false;
        }

        return true;
    }
}

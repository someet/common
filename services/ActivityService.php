<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 6:07 PM
 */

namespace someet\common\services;

use someet\common\models\Activity;

class ActivityService extends Activity
{
    use ServiceError;

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

        $model->status = Activity::STATUS_DELETE;
        if (!$model->save()) {
            $this->setError('删除失败');
            return false;
        }

        return true;
    }
}

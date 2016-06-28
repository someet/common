<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 6:00 PM
 */

namespace someet\common\services;


use someet\common\models\ActivityType;

class ActivityTypeService extends BaseService
{

    /**
     * 删除活动分类
     *
     * @param int $id 活动分类ID
     * @return array
     * @throws ServerErrorHttpException
     * @throws \Exception
     */
    public function deleteType($id)
    {
        $model = ActivityType::findOne($id);
        if (!$model) {
            $this->setError('活动分类不存在');
            return false;
        }

        // 检查该类型下是否有活动, 如果有则提示不能删除
        if (Activity::findOne(['type_id' => $id])) {
            $this->setError('当前分类下面有活动,无法删除活动分类');
            return false;
        }

        if ($model->delete() === false) {
            $this->setError('删除活动分类失败');
            return false;
        }

        return true;
    }
}

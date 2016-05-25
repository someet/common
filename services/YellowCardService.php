<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 5:53 PM
 */

namespace someet\common\services;

use someet\common\models\YellowCard;

class YellowCardService extends YellowCard
{
    use ServiceError;

    /**
     * 黄牌申诉
     *
     * @param int $id 黄牌ID
     * @param string $appeal_reason 申诉理由
     * @return array
     */
    public function appeal($id, $appeal_reason)
    {
        if (empty($appeal_reason)) {
            $this->setError('参数不正确或申请理由不为空');
            return false;
        }

        $yellowCard = YellowCard::findOne($id);
        if (!$yellowCard) {
            $this->setError('黄牌不存在');
            return false;
        }

        $yellowCard->appeal_reason = $appeal_reason;
        $yellowCard->appeal_status = YellowCard::APPEAL_STATUS_YES;
        $yellowCard->appeal_time = time();
        if (!$yellowCard->save()) {
            $this->setError('申诉失败');
            return false;
        }

        return true;
    }
}

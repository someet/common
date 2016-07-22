<?php
namespace someet\common\services;

use dektrium\user\models\Account;
use someet\common\models\Activity;
use someet\common\models\Noti;
use someet\common\models\NotificationTemplate;
use someet\common\models\QuestionItem;
use someet\common\models\AnswerItem;
use someet\common\models\Answer;
use someet\common\models\User;
use someet\common\services\ActivityService;
use someet\common\services\AnswerService;
use someet\common\models\ActivityType;
use someet\common\models\YellowCard;
use yii\web\Response;
use Yii;
use yii\db\ActiveQuery;

/**
 * 在报名时触发事件
 * @author  stark
 */
class EventService extends BaseService
{
    /**
     * 前台：报名以后执行的事件
     * @param  init $activity_id 活动id
     * @return 是否执行成功
     */
    public static function applyAfter($activity_id)
    {
        // 更新活动是否报满
        AnswerService::updateIsfull($activity_id);

        // 更新活动的报名率
        ActivityService::updateRepalyRate($activity_id);
    }

    /**
     * 前台：报名以前执行的事件
     * @param  init $activity_id 活动id
     * @return 是否执行成功
     */
    public static function applyBefore()
    {
        /*
        报名前执行的事件 检测
        是否和自己报名过的活动冲突，
        活动报满后不可以报名，
        活动只要是非发布状态都不可以报名
        */
        return AnswerService::checkApply($activity_id);
    }


    /**
     * 前台：pma和发起人筛选执行的事件
     * @return 是否执行成功
     */
    public static function managerFilter()
    {
    }

    /**
     * 后台：报名名额改动
     * @return 是否执行成功
     */
    public static function applyLimit($activity_id)
    {
        // 更新活动是否报满
        AnswerService::updateIsfull($activity_id);

        // 更新活动的报名率
        ActivityService::updateRepalyRate($activity_id);

        //查询现在的活动人数是否已经报满
        $activity = Activity::findOne($id);
        $is_full = $activity->join_people_count < $data['peoples'] ? Activity::IS_FULL_NO : Activity::IS_FULL_YES;

        //尝试更新活动是否已报名完成字段
        //如果 is_full 和之前的值一样则无需要更新
        if ($is_full != $activity->is_full) {
            //如果活动更新成功，更新活动当更新成功返回0,所以取反表示活动更新成功
            if (0 == Activity::updateAll(['is_full' => $is_full], ['id' => $id])) {
                //更新错误
                throw new DataValidationFailedException('更新活动数量失败');
            }
        }
    }

    /**
     * 后台：理想人数改动
     * @return 是否执行成功
     */
    public static function idealLimit()
    {
    }
}

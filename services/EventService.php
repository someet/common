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
use yii\db\ActiveQuery;
use Yii;

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

        //发送短信

        //发送微信push

        //极光push
    }

    /**
     * 前台：报名以前执行的事件
     * @param  init $activity_id 活动id
     * @return 是否执行成功
     */
    public static function applyBefore($activity_id)
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
     * 前台：取消报名
     * @param  init $activity_id 活动id
     * @return 是否执行成功
     */
    public static function cancelApply($activity_id)
    {
        // 更新活动是否报满
        AnswerService::updateIsfull($activity_id);

        // 更新活动的报名率
        ActivityService::updateRepalyRate($activity_id);
    }

    /**
     * 前台：请假事件
     * @param  init $activity_id 活动id
     * @return 是否执行成功
     */
    public static function askForLeave($activity_id)
    {
        // 更新活动是否报满
        AnswerService::updateIsfull($activity_id);

        // 更新活动的报名率
        ActivityService::updateRepalyRate($activity_id);
    }

    /**
     * 前台：发起人通过活动报名申请
     * @return 是否执行成功
     */
    public static function filterPass($activity_id)
    {
        // 更新活动是否报满
        AnswerService::updateIsfull($activity_id);

        // 更新活动的报名率
        ActivityService::updateRepalyRate($activity_id);
    }

    /**
     * 前台：发起人拒绝活动报名申请
     * @return 是否执行成功
     */
    public static function filterReject($activity_id)
    {
        // 更新活动是否报满
        AnswerService::updateIsfull($activity_id);

        // 更新活动的报名率
        ActivityService::updateRepalyRate($activity_id);
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
    }

    /**
     * 后台：理想人数改动
     * @return 是否执行成功
     */
    public static function idealLimit($activity_id)
    {
        // 更新活动是否报满字段
        AnswerService::updateIsfull($activity_id);
    }
}

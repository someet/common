<?php
namespace someet\common\services;

use someet\common\models\YellowCard;
use someet\common\models\Answer;
use someet\common\models\AppPush;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use Yii;
/**
 * @Author: wshudong
 * @Date:   2016-07-01 14:49:06
 * @Last Modified by:   wshudong
 * @Last Modified time: 2016-07-01 16:32:16
 */

class JiguangService  extends BaseService
{
	 /**
     * 根据发送信息类型匹配存入哪个模板
     * $type 1表示通过 2 表示失败
     * @return bool 值 是否存入成功
     */
    public static function pushMsg($user, $type, $model)
    {	
    	$from_type  = 'activity';
    	// 报名通过的短信内容
    	if ($type == Answer::STATUS_REVIEW_PASS) {
    		$content = self::fetchSuccessSmsData($model->activity->start_time, $model->activity->title);
    	}else if ($type == Answer::STATUS_REVIEW_REJECT) { //报名失败的短信内容
    		$content = self::fetchFailSmsData($model->activity->start_time, $model->activity->title, $model->reject_reason);
    	}

    	$msgExists = AppPush::find()->where(['from_id' => $model->activity->id, 'user_id' => $user->id, 'from_status' => $type])->exists();
    	if (!$msgExists) {
	    	if (!empty($content)) {
		    	$AppPush = new AppPush();
		    	$AppPush->content = $content;
		    	$AppPush->created_at = time();
		    	$AppPush->user_id = $user->id;
                $AppPush->from_type = $from_type;
		    	$AppPush->from_status = $type;
		    	$AppPush->from_id = $model->activity->id;
		    	$AppPush->save();
				return $AppPush;
	    	}
    	}
		

    }


    /**
     * 获取成功的短信内容
     * @param integer $start_time 活动开始时间
     * @param string $activity_name 活动名称
     * @return string 短信内容
     */
    public static function fetchSuccessSmsData($start_time, $activity_name)
    {
        $start_time = $start_time > 0 ? date('n月j日', $start_time) : '';
        //获取通过的短信模板
        return "恭喜，你报名的{$start_time}“{$activity_name}”活动已通过筛选。详情请到微信公众号(SomeetInc)查看。";
    }
    /**
     * 获取失败的短信内容
     * @param integer $start_time 活动开始时间
     * @param string $activity_name 活动名称
     * @param string $reason 被拒绝的原因
     * @return string 失败的短信内容
     */
    public static function fetchFailSmsData($start_time, $activity_name, $reason = '')
    {
        $start_time = $start_time > 0 ? date('n月j日', $start_time) : '';
        //获取拒绝的短信模板
        return "Shit happens！{$reason}很抱歉你报名的{$start_time}“{$activity_name}”活动未通过筛选。祝下次好运。详情请到微信公众号(SomeetInc)查看。";
    }

    /**
     * 获取通知参加活动的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    public static function fetchNotiSmsData($activity_name, $start_time, $weather)
    {
        //获取通知参加活动的短信
        return "你报名的活动“{$activity_name}”在今天的{$start_time}开始。{$weather}请合理安排时间出行，不要迟到哦。";
    }

    /**
     * 获取需要反馈的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    public static function fetchNeedFeedbackSmsData($activity_name)
    {
        //获取需要反馈的短信内容
        return " 你好，你已成功参加“{$activity_name}”活动，请及时对活动进行反馈，之后会提高下次通过筛选概率哦。";
    }
}
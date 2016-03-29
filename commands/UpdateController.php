<?php
namespace app\commands;

use someet\common\models\Activity;
use Yii;
use someet\common\models\Answer;
use dektrium\user\models\Account;
use someet\common\models\User;
use someet\common\models\UgaAnswer;
use someet\common\models\UgaQuestion;
use someet\common\models\YellowCard;
use someet\common\models\ActivityFeedback;
/**
* 用来更新数据
* 执行方式 在命令行
* 如： docker exec -i backend_app_1 ./yii update/user-join-count（控制器/方法）
* 可以用 yii help 来提示帮助
*/
class UpdateController  extends \yii\console\Controller
{

	/**
    * 每周一凌晨更新 黑牌
	* 执行方式 在命令行
	* 如： docker exec -i backend_app_1 ./yii update/update-black-label（控制器/方法）
	* 可以用 yii help 来提示帮助
	*/
	public function actionUpdateBlackLabel()
	{
		// 每周一 2点执行更新 黑牌 主要更新本周一凌晨往前28天的数据
		// 根据用户id 查询出每周黄牌的数量，超过三个，则更新 user表里面的是否被拉黑字段
		$yellowCard = YellowCard::find()
					->select('id,user_id , sum(card_num) card_count')
					// ->where(['status' => YellowCard::STATUS_NORMAL])
					->where('card_category > 0')
					// 上周一凌晨前28天到上周一凌晨
	                ->andWhere('created_at > (' .getLastEndTime().' - 2419200) and '.'created_at < ' .getLastEndTime())
	                ->asArray()
	                ->groupBy('user_id')
	                ->all();
	     if (!empty($yellowCard)) {
		     foreach ($yellowCard as  $value) {
				if ($value['card_count'] >= 3) {
					User::updateAll([
						'black_label' => User::BLACK_LIST_YES,
						'black_time' => time(),
						],['id' => $value['user_id']]);

					}
		     }
	     }

	     // 解禁黑牌

	     // 如黑牌创建时间超过了28天则解禁
	     $userBlack = User::find()
	     				->where(['black_label' => User::BLACK_LIST_YES])
	     				->andWhere('('.getLastEndTime() .' - 2419200 ) > black_time')
	     				->all();

	    if (!empty($userBlack)) {
			foreach ($userBlack as  $userBlackValue) {
				User::updateAll([
					'black_label' => User::BLACK_LIST_NO,
					],['id' => $userBlackValue['id']]);

		     }
	     }

 		return true;
	}

	/**
    * 每周一凌晨更新 黄牌数量
	* 执行方式 在命令行
	* 如： docker exec -i backend_app_1 ./yii update/yellow-card（控制器/方法）
	* 可以用 yii help 来提示帮助
	*/
    public function actionYellowCard()
    {
    	// 查出answer表里面满足黄牌的数量

	    // 请假的黄牌统计 小于24 小时 记录两张黄牌
	    $leave_yet_in_one_day = Answer::find()
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'leave_status' => Answer::STATUS_LEAVE_YES,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	])
	                //获取上周的数据
	                ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
	                
	                // 活动请假时间在活动开始时间减去24小时和活动开始之间
	                ->andWhere('answer.leave_time > (activity.start_time - 86400)' .' and '.'answer.leave_time < activity.start_time')
	                ->asArray()
	                ->all();

	    // 检测数据是否为空
	    if (!empty($leave_yet_in_one_day)) {
		    foreach ($leave_yet_in_one_day as  $leave_yet_in_one_day_value) {
		    	$YellowCard_leave_yet_in_one_day = new YellowCard();

		    	// 判断数据之前是否更新过，如果更新过则不再更新，防止重复更新
		    	$leave_yet_in_one_day_exists = YellowCard::find()
							    	->where(['user_id' => $leave_yet_in_one_day_value['user_id'],'activity_id' => $leave_yet_in_one_day_value['activity']['id']])
							    	->exists();
				if (!$leave_yet_in_one_day_exists) {
			    	$YellowCard_leave_yet_in_one_day->activity_id = $leave_yet_in_one_day_value['activity_id'];
			    	$YellowCard_leave_yet_in_one_day->activity_title = $leave_yet_in_one_day_value['activity']['title'];
			    	$YellowCard_leave_yet_in_one_day->username = $leave_yet_in_one_day_value['user']['username'];
			    	$YellowCard_leave_yet_in_one_day->card_category = YellowCard::CARD_CATEGOTY_LEAVE_2;
			    	$YellowCard_leave_yet_in_one_day->card_num = YellowCard::CARD_NUM_LEAVE_2;
			    	$YellowCard_leave_yet_in_one_day->created_at = time();
			    	$YellowCard_leave_yet_in_one_day->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_leave_yet_in_one_day->user_id = $leave_yet_in_one_day_value['user_id'];
					$YellowCard_leave_yet_in_one_day->save();
	        	}
		    }
	    }

	    // 请假的黄牌统计 大于24 小时
	    $leave_yet_no_one_day = Answer::find()
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'leave_status' => Answer::STATUS_LEAVE_YES,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	])
	                ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
	                // 活动请假时间大于 在活动开始时间减去24小时
					->andWhere('answer.leave_time < (activity.start_time - 86400)')
					->asArray()
	                ->all();

	    if (!empty($leave_yet_no_one_day)) {
		    foreach ($leave_yet_no_one_day as  $leave_yet_no_one_day_value) {
		    	$YellowCard_leave_yet_no_one_day = new YellowCard();
		    	$leave_yet_no_one_day_exists = YellowCard::find()
							    	->where(['user_id' => $leave_yet_no_one_day_value['user_id'],'activity_id' => $leave_yet_no_one_day_value['activity']['id']])
							    	->exists();
				if (!$leave_yet_no_one_day_exists) {
			    	$YellowCard_leave_yet_no_one_day->activity_id = $leave_yet_no_one_day_value['activity_id'];
			    	$YellowCard_leave_yet_no_one_day->activity_title = $leave_yet_no_one_day_value['activity']['title'];
			    	$YellowCard_leave_yet_no_one_day->username = $leave_yet_no_one_day_value['user']['username'];
			    	$YellowCard_leave_yet_no_one_day->card_category = YellowCard::CARD_CATEGOTY_LEAVE_1;
			    	$YellowCard_leave_yet_no_one_day->card_num = YellowCard::CARD_NUM_LEAVE_1;
			    	$YellowCard_leave_yet_no_one_day->created_at = time();
			    	$YellowCard_leave_yet_no_one_day->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_leave_yet_no_one_day->user_id = $leave_yet_no_one_day_value['user_id'];
					$YellowCard_leave_yet_no_one_day->save();
	        	}
		    }
		}


	   	// 迟到的黄牌统计
	    $arrive_yet = Answer::find()
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'arrive_status' => Answer::STATUS_ARRIVE_LATE,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	])
	                ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
					->asArray()
	                ->all();
	    if (!empty($arrive_yet)) {
		    foreach ($arrive_yet as  $arrive_yet_value) {
		    	$YellowCard_arrive_yet = new YellowCard();
		    	$arrive_yet_exists = YellowCard::find()
							    	->where(['user_id' => $arrive_yet_value['user_id'],'activity_id' => $arrive_yet_value['activity']['id']])
							    	->exists();
				if (!$arrive_yet_exists) {
			    	$YellowCard_arrive_yet->activity_id = $arrive_yet_value['activity_id'];
			    	$YellowCard_arrive_yet->activity_title = $arrive_yet_value['activity']['title'];
			    	$YellowCard_arrive_yet->username = $arrive_yet_value['user']['username'];
			    	$YellowCard_arrive_yet->card_category = YellowCard::CARD_CATEGOTY_LATE;
			    	$YellowCard_arrive_yet->card_num = YellowCard::CARD_NUM_LATE;
			    	$YellowCard_arrive_yet->created_at = time();
			    	$YellowCard_arrive_yet->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_arrive_yet->user_id = $arrive_yet_value['user_id'];
		    		$YellowCard_arrive_yet->save();
	        	}
		    }
	    }


	   	// 爽约的黄牌统计
	    $arrive_no = Answer::find()
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'answer.arrive_status' => Answer::STATUS_ARRIVE_YET,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	'answer.apply_status' => Answer::APPLY_STATUS_YES,
	                	])
	                ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
					->asArray()
	                ->all();
	    if (!empty($arrive_no)) {
		    foreach ($arrive_no as  $arrive_no_value) {
		    	$YellowCard_arrive_no = new YellowCard();
		    	$arrive_yet_exists = YellowCard::find()
							    	->where(['user_id' => $arrive_no_value['user_id'],'activity_id' => $arrive_no_value['activity']['id']])
							    	->exists();
				if (!$arrive_yet_exists) {
			    	$YellowCard_arrive_no->activity_id = $arrive_no_value['activity_id'];
			    	$YellowCard_arrive_no->activity_title = $arrive_no_value['activity']['title'];
			    	$YellowCard_arrive_no->username = $arrive_no_value['user']['username'];
			    	$YellowCard_arrive_no->card_category = YellowCard::CARD_CATEGOTY_NO;
			    	$YellowCard_arrive_no->card_num = YellowCard::CARD_NUM_NO;
			    	$YellowCard_arrive_no->created_at = time();
			    	$YellowCard_arrive_no->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_arrive_no->user_id = $arrive_no_value['user_id'];
					$YellowCard_arrive_no->save();
	        	}
		    }
	    }

    }

	/**
	*更新用户参加次数
	*/
	public function actionUserJoinCount()
	{
		$answerJoin = Answer::find()
						->select([
							'user_id',
							'COUNT(user_id) as join_count'
						])
						->asArray()
						->groupBy('user_id')
						->all();

		foreach ($answerJoin as $answer) {
			if (empty($answer['user_id'])) {
				continue;
			}

			User::updateAll(
					['join_count' => $answer['join_count']],
					['id' => $answer['user_id']]
				);
		}

        return true;

    }

    /**
    * 更新回答问题的总数
	* 执行方式 在命令行
	* 如： docker exec -i backend_app_1 ./yii update/answer-num（控制器/方法）
	* 可以用 yii help 来提示帮助
	*/
	public function actionAnswerNum()
	{
		$answerNum = UgaAnswer::find()
						->select([
							'question_id',
							'COUNT(id) as answer_num'
						])
						->asArray()
						->groupBy('question_id')
						->all();

		foreach ($answerNum as $answer) {
			if (empty($answer['question_id'])) {
				continue;
			}

			UgaQuestion::updateAll(
					['answer_num' => $answer['answer_num']],
					['id' => $answer['question_id']]
				);
		}

        return true;

    }

    /**
    * 在answer表里面更新是否反馈  is_feedback
	* 执行方式 在命令行
	* 如： docker exec -i backend_app_1 ./yii update/is-feedback（控制器/方法）
	* 可以用 yii help 来提示帮助
    */

	public function actionIsFeedback()
	{
		$activity_feedback = ActivityFeedback::find()->asArray()->all();
		foreach ($activity_feedback as $key => $value) {
			Answer::updateAll(['is_feedback' => Answer::FEEDBACK_IS ],['user_id' => $value['user_id'],'activity_id' => $value['activity_id']]);
		}

		return true;
	}

	/**
	*更新活动的星期字段值
	* 执行方式 在命令行
	* 如： docker exec -i backend_app_1 ./yii update/activity-week（控制器/方法）
	* 可以用 yii help 来提示帮助
	*/
	public function actionActivityWeek()
	{
		$activities = Activity::find()
				->asArray()
				->all();

		foreach ($activities as $activity) {
			if (0 == $activity['start_time']) {
				continue;
			}

			Activity::updateAll(
				['week' => date('w', $activity['start_time'])],
				['id' => $activity['id']]
			);
		}

		return true;
	}
}

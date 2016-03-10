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
    * 每周一凌晨更新 黄牌数量
	* 执行方式 在命令行 
	* 如： docker exec -i backend_app_1 ./yii update/yellow-card（控制器/方法） 
	* 可以用 yii help 来提示帮助
	*/
    public function actionYellowCard(){
    	// 查出answer表里面满足黄牌的数量

	    // 请假的黄牌统计 小于24 小时
	    $leave_yet_in_one_day = Answer::find() 
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'leave_status' => Answer::STATUS_LEAVE_YET,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	])
	                ->andWhere('answer.created_at > ' .getWeekBefore().' and '.'answer.created_at < ' .getLastEndTime())
	                ->andWhere('answer.leave_time > (activity.start_time - 3600)' .' and '.'answer.leave_time < activity.start_time')
	                ->asArray()
	                ->all();
	    if (!empty($leave_yet_in_one_day)) { 
		    foreach ($leave_yet_in_one_day as  $leave_yet_in_one_day_value) {
		    	$YellowCard_leave_yet_in_one_day = new YellowCard();
		    	$leave_yet_in_one_day_exists = YellowCard::find()
							    	->where(['user_id' => $leave_yet_in_one_day_value['user_id'],'activity_id' => $leave_yet_in_one_day_value['activity']['id']])
							    	->exists();
				if (!$leave_yet_in_one_day_exists) {
			    	$YellowCard_leave_yet_in_one_day->activity_id = $leave_yet_in_one_day_value['activity_id'];
			    	$YellowCard_leave_yet_in_one_day->activity_title = $leave_yet_in_one_day_value['activity']['title'];
			    	$YellowCard_leave_yet_in_one_day->username = $leave_yet_in_one_day_value['user']['username'];
			    	$YellowCard_leave_yet_in_one_day->card_category = YellowCard::CARD_CATEGOTY_LEAVE;
			    	$YellowCard_leave_yet_in_one_day->card_num = YellowCard::CARD_NUM_LEAVE_IN_24_MIN;
			    	$YellowCard_leave_yet_in_one_day->created_at = time();
			    	$YellowCard_leave_yet_in_one_day->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_leave_yet_in_one_day->user_id = $leave_yet_in_one_day_value['user_id'];
		    	//开启事务
	        	// $transaction = $YellowCard_leave_yet_in_one_day->getDb()->beginTransaction();
		    	$YellowCard_leave_yet_in_one_day->save();
	        	// $transaction->commit();
	        	}
		    }
	    }

	    // 请假的黄牌统计 大于24 小时
	    $leave_yet_no_one_day = Answer::find() 
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'leave_status' => Answer::STATUS_LEAVE_YET,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	])
	                ->andWhere('answer.created_at > ' .getWeekBefore().' and '.'answer.created_at < ' .getLastEndTime())
					->andWhere('answer.leave_time < (activity.start_time - 3600)')	                
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
			    	$YellowCard_leave_yet_no_one_day->card_category = YellowCard::CARD_CATEGOTY_LEAVE;
			    	$YellowCard_leave_yet_no_one_day->card_num = YellowCard::CARD_NUM_LEAVE_NO_24_MIN;
			    	$YellowCard_leave_yet_no_one_day->created_at = time();
			    	$YellowCard_leave_yet_no_one_day->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_leave_yet_no_one_day->user_id = $leave_yet_no_one_day_value['user_id'];
		    	//开启事务
	        	// $transaction = $YellowCard_leave_yet_in_one_day->getDb()->beginTransaction();
		    	$YellowCard_leave_yet_no_one_day->save();
	        	// $transaction->commit();
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
	                ->andWhere('answer.created_at > ' .getWeekBefore().' and '.'answer.created_at < ' .getLastEndTime())            
					->asArray()
	                ->all();
	    if (empty($arrive_yet)) {
		    foreach ($arrive_yet as  $arrive_yet_value) {
		    	$YellowCard_arrive_yet = new YellowCard();
		    	$arrive_yet_exists = YellowCard::find()
							    	->where(['user_id' => $arrive_yet_value['user_id'],'activity_id' => $arrive_yet_value['activity']['id']])
							    	->exists();
				if (!$arrive_yet_exists) {
			    	$YellowCard_arrive_yet->activity_id = $arrive_yet_value['activity_id'];
			    	$YellowCard_arrive_yet->activity_title = $arrive_yet_value['activity']['title'];
			    	$YellowCard_arrive_yet->username = $arrive_yet_value['user']['username'];
			    	$YellowCard_arrive_yet->card_category = YellowCard::CARD_CATEGOTY_LEAVE;
			    	$YellowCard_arrive_yet->card_num = YellowCard::CARD_NUM_LATE;
			    	$YellowCard_arrive_yet->created_at = time();
			    	$YellowCard_arrive_yet->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_arrive_yet->user_id = $arrive_yet_value['user_id'];
		    	//开启事务
	        	// $transaction = $YellowCard_leave_yet_in_one_day->getDb()->beginTransaction();
		    	$YellowCard_arrive_yet->save();
	        	// $transaction->commit();
	        	}
		    }
	    }        


	   	// 爽约的黄牌统计
	    $arrive_no = Answer::find() 
	    			->with(['user','user.profile','activity'])
	    			->joinWith('activity')
	                ->where([
	                	'arrive_status' => Answer::STATUS_ARRIVE_YET,
	                	'answer.status' => Answer::STATUS_REVIEW_PASS,
	                	])
	                ->andWhere('answer.created_at > ' .getWeekBefore().' and '.'answer.created_at < ' .getLastEndTime())            
					->asArray()
	                ->all();
	    if (empty($arrive_no)) {
		    foreach ($arrive_no as  $arrive_no_value) {
		    	$YellowCard_arrive_no = new YellowCard();
		    	$arrive_yet_exists = YellowCard::find()
							    	->where(['user_id' => $arrive_no_value['user_id'],'activity_id' => $arrive_no_value['activity']['id']])
							    	->exists();
				if (!$arrive_yet_exists) {
			    	$YellowCard_arrive_no->activity_id = $arrive_no_value['activity_id'];
			    	$YellowCard_arrive_no->activity_title = $arrive_no_value['activity']['title'];
			    	$YellowCard_arrive_no->username = $arrive_no_value['user']['username'];
			    	$YellowCard_arrive_no->card_category = YellowCard::CARD_CATEGOTY_LEAVE;
			    	$YellowCard_arrive_no->card_num = YellowCard::CARD_NUM_NO;
			    	$YellowCard_arrive_no->created_at = time();
			    	$YellowCard_arrive_no->status = YellowCard::STATUS_NORMAL;
			    	$YellowCard_arrive_no->user_id = $arrive_no_value['user_id'];
		    	//开启事务
	        	// $transaction = $YellowCard_leave_yet_in_one_day->getDb()->beginTransaction();
		    	$YellowCard_arrive_no->save();
	        	// $transaction->commit();
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
}




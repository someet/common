<?php
namespace app\commands;

use someet\common\models\Activity;
use Yii;
use someet\common\models\Answer;
use dektrium\user\models\Account;
use someet\common\models\User;
use someet\common\models\UgaAnswer;
use someet\common\models\UgaQuestion;
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




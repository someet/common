<?php
namespace app\commands;

use someet\common\models\Activity;
use Yii;
use someet\common\models\Answer;
use dektrium\user\models\Account;
use someet\common\models\User;
use someet\common\models\UgaAnswer;
use someet\common\models\UgaQuestion;
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
    *更新回答问题的总数
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


}




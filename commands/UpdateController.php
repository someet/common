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
use someet\common\models\RActivityFounder;

/**
* 用来更新数据
* 执行方式 在命令行
* 如： docker exec -i backend_app_1 ./yii update/user-join-count（控制器/方法）
* 可以用 yii help 来提示帮助
*/
class UpdateController extends \yii\console\Controller
{
    /**
     * 更新发起人数据 与 新建立的表保持一致
     * docker exec -i backend_app_1 ./yii update/update-founder
     */
    public function actionUpdateFounder()
    {

        $activity = Activity::find()->asArray()->all();
        foreach ($activity as $key => $value) {
            if (!empty($value['co_founder1'])) {
                $RActivityFounder1 = RActivityFounder::find()
                                    ->where([
                                        'activity_id'=>$value['id'],
                                        'founder_id' => $value['co_founder1']
                                        ])
                                    ->exists();
                if (!$RActivityFounder1) {
                    $founder = new RActivityFounder();
                    $founder->activity_id = $value['id'];
                    $founder->founder_id = $value['co_founder1'];
                    $founder->save();
                }
            }

            if (!empty($value['co_founder2'])) {
                $RActivityFounder2 = RActivityFounder::find()
                    ->where([
                        'activity_id'=>$value['id'],
                        'founder_id' => $value['co_founder2']
                        ])
                    ->exists();
                if (!$RActivityFounder2) {
                    $founder = new RActivityFounder();
                    $founder->activity_id = $value['id'];
                    $founder->founder_id = $value['co_founder2'];
                    $founder->save();
                }
            }

            if (!empty($value['co_founder3'])) {
                $RActivityFounder3 = RActivityFounder::find()
                    ->where([
                        'activity_id'=>$value['id'],
                        'founder_id' => $value['co_founder3']
                        ])
                    ->exists();
                if (!$RActivityFounder3) {
                    $founder = new RActivityFounder();
                    $founder->activity_id = $value['id'];
                    $founder->founder_id = $value['co_founder3'];
                    $founder->save();
                }
            }

            if (!empty($value['co_founder4'])) {
                $RActivityFounder4 = RActivityFounder::find()
                    ->where([
                        'activity_id'=>$value['id'],
                        'founder_id' => $value['co_founder4']
                        ])
                    ->exists();
                if (!$RActivityFounder4) {
                    $founder = new RActivityFounder();
                    $founder->activity_id = $value['id'];
                    $founder->founder_id = $value['co_founder4'];
                    $founder->save();
                }
            }
        }
        return true;
    }

    /**
     * 更新用户的报名状态（当活动已经开始）
     * 执行方式 在命令行
     * 如： docker exec -i backend_app_1 ./yii update/update-user-answer-status（控制器/方法）
     * 可以用 yii help 来提示帮助
     */
    public function actionUpdateUserAnswerStatus()
    {

        //select * from answer left join activity on answer.activity_id = activity.id where answer.status = 10 and activity.`start_time` < 1460356310

        //找出本周之前的活动中报名状态为未报名的信息
        $answers = Answer::find()
                ->join('LEFT JOIN', 'activity', 'activity.id = answer.activity_id')
                ->where('activity.start_time < ' . getLastEndTime())
                ->andWhere(['answer.status' => Answer::STATUS_REVIEW_YET])
                ->asArray()
                ->all();

        //获取报名的id列表
        $answer_ids = array_column($answers, 'id');

        if (is_array($answer_ids) && count($answer_ids)>0) {
            //统一更新是否已发送和发送时间，以及状态为拒绝
            Answer::updateAll(['is_send' => 1, 'send_at' => 1, 'status' => Answer::STATUS_REVIEW_REJECT], ['in', 'id', $answer_ids]);
        }

    }
    /**
     * 修复黑牌更新时间 弥补之前以为更新时间不对的bug
     * 如： docker exec -i backend_app_1 ./yii update/fix-black-label（控制器/方法）
     * @return 更新函数 不需要返回
     */
    public function actionFixBlackLabel()
    {
        // 查出黄牌在一个月之内大于等于三张的最后一张的黄牌更新时间 来更新之前的黑牌时间
        $yellowCard = YellowCard::find()
                        ->select('id,user_id,sum(card_num) card_count,created_at')
                        ->where("card_category > 0")
                        ->andWhere('created_at > (' .getLastEndTime().' - 5184000) and '.'created_at < ' .getLastEndTime())
                        ->asArray()
                        ->groupBy('user_id')
                        ->all();
        if (!empty($yellowCard)) {
            foreach ($yellowCard as $value) {
                if ($value['card_count'] >= 3) {
                    User::updateAll([
                        'black_time' => $value['created_at'],
                        ], ['id' => $value['user_id']]);
                }
            }
        }

         return true;
    }
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
        // 2016年05月04日11:09:59 wangshudong 修复 增加在这个期间排除用户已经为黑牌状态，不然还会更新黑牌时间
        $yellowCard = YellowCard::find()
                    ->select('yellow_card.id,yellow_card.user_id , sum(yellow_card.card_num) card_count')
                    // ->where(['status' => yellow_card::STATUS_NORMAL])
                    ->joinWith('user')
                    ->where('yellow_card.card_category > 0')
                    ->andWhere(['user.black_label' => User::BLACK_LIST_NO])
                    // 上周一凌晨前28天到上周一凌晨
                    ->andWhere('yellow_card.created_at > (' .getLastEndTime().' - 2419200) and '.'yellow_card.created_at < ' .getLastEndTime())
                    ->asArray()
                    ->groupBy('yellow_card.user_id')
                    ->all();
        if (!empty($yellowCard)) {
            foreach ($yellowCard as $value) {
                if ($value['card_count'] >= 3) {
                    User::updateAll([
                        'black_label' => User::BLACK_LIST_YES,
                        'black_time' => time(),
                        ], ['id' => $value['user_id']]);
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
            foreach ($userBlack as $userBlackValue) {
                User::updateAll([
                    'black_label' => User::BLACK_LIST_NO,
                    ], ['id' => $userBlackValue['id']]);
            }
        }

        return true;
    }

    /**
    * 黄牌数量
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
                        'answer.leave_status' => Answer::STATUS_LEAVE_YES,
                        'answer.status' => Answer::STATUS_REVIEW_PASS,
                        ])
                    //获取上周一的凌晨到上周一凌晨前28天的数据
                    ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
                    
                    // 活动请假时间在活动开始时间减去24小时和活动开始之间
                    ->andWhere('answer.leave_time > (activity.start_time - 86400)' .' and '.'answer.leave_time < activity.start_time')
                    ->asArray()
                    ->all();

        // 检测数据是否为空
        if (!empty($leave_yet_in_one_day)) {
            foreach ($leave_yet_in_one_day as $leave_yet_in_one_day_value) {
                // 判断数据之前是否更新过，如果更新过则不再更新，防止重复更新
                $leave_yet_in_one_day_exists = YellowCard::find()
                                    ->where(['user_id' => $leave_yet_in_one_day_value['user_id'],'activity_id' => $leave_yet_in_one_day_value['activity']['id']])
                                    ->exists();
                if (!$leave_yet_in_one_day_exists) {
                    $YellowCard_leave_yet_in_one_day = new YellowCard();
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
                    //获取上周一的凌晨到上周一凌晨前28天的数据
                    ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
                    // 活动请假时间大于 在活动开始时间减去24小时
                    ->andWhere('answer.leave_time < (activity.start_time - 86400)')
                    ->asArray()
                    ->all();

        if (!empty($leave_yet_no_one_day)) {
            foreach ($leave_yet_no_one_day as $leave_yet_no_one_day_value) {
                $leave_yet_no_one_day_exists = YellowCard::find()
                                    ->where(['user_id' => $leave_yet_no_one_day_value['user_id'],'activity_id' => $leave_yet_no_one_day_value['activity']['id']])
                                    ->exists();
                if (!$leave_yet_no_one_day_exists) {
                    $YellowCard_leave_yet_no_one_day = new YellowCard();
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
                    //获取上周一的凌晨到上周一凌晨前28天的数据
                    ->andWhere('answer.created_at > (' .getLastEndTime().' - 2419200) and '.'answer.created_at < ' .getLastEndTime())
                    ->asArray()
                    ->all();
        if (!empty($arrive_yet)) {
            foreach ($arrive_yet as $arrive_yet_value) {
                $arrive_yet_exists = YellowCard::find()
                                    ->where(['user_id' => $arrive_yet_value['user_id'],'activity_id' => $arrive_yet_value['activity']['id']])
                                    ->exists();
                if (!$arrive_yet_exists) {
                    $YellowCard_arrive_yet = new YellowCard();
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
            foreach ($arrive_no as $arrive_no_value) {
                $arrive_yet_exists = YellowCard::find()
                                    ->where(['user_id' => $arrive_no_value['user_id'],'activity_id' => $arrive_no_value['activity']['id']])
                                    ->exists();
                if (!$arrive_yet_exists) {
                    $YellowCard_arrive_no = new YellowCard();
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
            Answer::updateAll(['is_feedback' => Answer::FEEDBACK_IS ], ['user_id' => $value['user_id'],'activity_id' => $value['activity_id']]);
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

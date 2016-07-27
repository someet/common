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
use someet\common\models\ActivityType;
use someet\common\models\YellowCard;
use someet\common\services\BackendEventService;
use yii\web\Response;
use Yii;
use yii\db\ActiveQuery;

class AnswerService extends BaseService
{
    /**
     * 活动报名时检测 活动是否报名 活动是与已报名的活动冲突 活动是否关闭 取消
     * @param  init $id 活动id
     * @return bool 返回布尔值
     */
    public static function checkApply($id)
    {
        $model = Activity::findOne($id);
        $is_apply = self::Isfull($id) == Activity::IS_FULL_YES
                    || self::applyConflict($id)['has_conflict'] == 2 // 活动冲突
                    || $model->status != Activity::STATUS_RELEASE //只要活动不是发布状态都不可以报名
                    ;
        return $is_apply ? Answer::APPLY_NO : Answer::APPLY_YES;
    }

    /**
     * 更新活动是否已满
     * @param  init $activity_id 活动id
     * @return bool 返回布尔值
     */
    public static function updateIsfull($activity_id)
    {
        if (self::Isfull($activity_id) == Activity::IS_FULL_YES) {
            $isfull = Activity::updateAll(['is_full' => Activity::IS_FULL_YES], ['id' => $activity_id]);
        } elseif (self::Isfull($activity_id) == Activity::IS_FULL_NO) {
            $isfull = Activity::updateAll(['is_full' => Activity::IS_FULL_NO], ['id' => $activity_id]);
        }
        return true;
    }

    /**
     * 判断活动是否已满
     * 已通过人数 - 已经请假人数 = 理想报名人数上限 不能再报名
     * （通过人数为零）待筛选人数 = 报名名额 不能再报名
     * （通过人数 - 请假人数 = N，N小于理想人数上限 即未达到2的标准）
     * 	待筛选人数不超过 min （（理想人数上限-N）*2，报名名额 - 理想人数上限）
     * @param  init $id 活动id
     * @return 返回 1不可以报名 或 0可以报名
     */
    public static function Isfull($activity_id)
    {
        $activity = Activity::findOne($activity_id);

        // 已通过人数
        $passCount = Answer::find()
                ->where([
                    'activity_id' => $activity_id,
                    'status' => Answer::STATUS_REVIEW_PASS,
                    ])
                ->count();

        //通过后请假人数
        $leaveCount = Answer::find()
                ->where([
                    'activity_id' => $activity_id,
                    'status' => Answer::STATUS_REVIEW_PASS,
                    'leave_status' => Answer::STATUS_LEAVE_YES
                    ])
                ->count();

        // 待筛选人数
        $answer_filter = Answer::find()->where([
                    'activity_id' => $activity_id,
                    'status' => Answer::STATUS_REVIEW_YET,
                    'apply_status' => Answer::APPLY_STATUS_YES,
                    ])
                    ->count();

        // 通过人数为零的情况下待筛选人数
        if ($passCount == 0) {
            $answer_filter = Answer::find()->where([
                        'activity_id' => $activity_id,
                        'status' => Answer::STATUS_REVIEW_YET,
                        'apply_status' => Answer::APPLY_STATUS_YES,
                        ])
                        ->count();
            // （通过人数为零）待筛选人数 = 报名名额 不能再报名
            if ($answer_filter == $activity->peoples) {
                return Activity::IS_FULL_YES;
            }
        }

        // 已通过人数 - 已经请假人数 = 理想报名人数上限 不能再报名
        if ($passCount - $leaveCount == $activity->ideal_number_limit) {
            return Activity::IS_FULL_YES;
        };

        // 真实报名的人数
        $actualPass = $passCount - $leaveCount;

        // （通过人数 - 请假人数 = N，N小于理想人数上限 即未达到2的标准）待筛选人数不超过 min （（理想人数上限-N）*2，报名名额 - 理想人数上限）
        $is_full =  $answer_filter < min(
                        (($activity->ideal_number_limit - $actualPass) * 2),
                        ($activity->peoples - $activity->ideal_number_limit)
                    )
                    ? Activity::IS_FULL_NO
                    : Activity::IS_FULL_YES;
        return $is_full;
    }

    /**
     * 活动报名时检测活动是否报满
     * @param  integer $id 活动id
     * @return json  返回与报名冲突的活动
     */
    public static function applyIsfull($activity_id)
    {

        // $model = Activity::find()->where(['is_full' => Activity::IS_FULL_YES, 'id' => $activity_id])->exists();

        $activity = Activity::findOne($activity_id);
        $count_join = Answer::find()->where(['activity_id' => $activity_id])->count();
        $isfull = $activity->peoples > $count_join ? Activity::IS_FULL_NO : Activity::IS_FULL_YES;

        return $isfull;
    }


    /**
     * 活动报名的冲突检测 检测与自己已经报名的活动是否冲突
     * @param  integer $id 活动id
     * @return json  返回与报名冲突的活动
     */
    public static function applyConflict($id)
    {
        $user_id = Yii::$app->user->id;
        //检查参数
        if (!is_numeric($id)) {
            throw new DataValidationFailedException('参数错误');
        }

        $timeDistinct = 1790; //1个小时

        //查询前活动的开始时间和结束时间分别是多少
        $currentActivity = Activity::findOne($id);
        if (empty($currentActivity)) {
            throw new ObjectNotExistsException('当前活动不存在');
        }

        //将开始时间-1小时，将结束时间添加1小时
        $startTime = $currentActivity->start_time - $timeDistinct;
        $endTime = $currentActivity->end_time + $timeDistinct;

        //获取隐藏的活动分类编号
        $activity_test_type_ids = ActivityType::find()->select('id')->where(['status' => ActivityType::STATUS_HIDDEN])->all();
        $activity_test_type_ids = is_array($activity_test_type_ids) ? array_column($activity_test_type_ids, 'id') : [];

        $activity = Activity::findOne($id);
        if (in_array($activity->type_id, $activity_test_type_ids)) {
            return ['has_conflict' => 0, 'activities' => null];
        }
        //查询活动开始时间-1小时大于最小时间，或者结束时间加1小时小于最大时间, 并且id不是当前活动id

        $conflictActivities = Activity::find()
            ->joinWith('type')
            ->where(['activity_type.status' => ActivityType::STATUS_NORMAL])
            ->andwhere(['between', 'start_time', $startTime, $endTime])
            ->orWhere(['between', 'end_time', $startTime, $endTime])
            ->andWhere('activity.id != ' . $id)
            ->andWhere(['activity.status'=>Activity::STATUS_RELEASE])
            ->asArray()
            ->all();

        //如果存在冲突的活动
        if (count($conflictActivities) > 0) {
            //获取活动id
            $activityIds = array_column($conflictActivities, 'id');

            //查询冲突相关的活动,有哪些是已经报名了的
            $answer = Answer::find()
                ->where(['in', 'activity_id', $activityIds])
                ->andWhere(['user_id' => $user_id])
                ->andWhere(['status' => Answer::STATUS_REVIEW_PASS])
                ->andWhere(['leave_status' => Answer::STATUS_LEAVE_YET])
                ->one();

            //如果已报名了活动, 返回已报名的列表
            if ($answer) {
                //获取冲突的活动
                foreach ($conflictActivities as $act) {
                    if ($act['id'] == $answer->activity_id) {
                        $activity = $act;
                        break;
                    }
                }
                return ['has_conflict' => 2, 'activities' => null, 'activity' => $activity];
            } else {
                return ['has_conflict' => 1, 'activities' => $conflictActivities];
            }
        } else {
            //不存在冲突的活动，可以正常进行报名
            return ['has_conflict' => 0, 'activities' => null];
        }
    }

    /**
     * 报名
     *
     * @param int $question_id 问题ID
     * @param int $activity_id 活动ID
     * @param array $post 提交的答案 For example:
     * {
     *      "question_id":"2",
     *      "activity_id":"78",
     *      "q1":{
     *          "question_item_id":"76",
     *          "question_value":"haha"
     *      },
     *      "q2":{
     *          "question_item_id":"77",
     *          "question_value":"hha"
     *      },
     *      "q3":{
     *          "question_item_id":"78",
     *          "question_value":"haa"
     *      }
     * }
     * @return array
     * @throws \yii\db\Exception
     */
    public function join($question_id, $activity_id, $post)
    {
        $user_id = Yii::$app->user->id;

        if (!is_array($post['q1']) || !is_array($post['q2']) || !is_array($post['q3'])) {
            $this->setError('三个问题不完整');
            return false;
        }

        $activity = Activity::findOne($activity_id);
        if (!$activity) {
            $this->setError('活动不存在');
            return false;
        }

        if ($activity->status == Activity::STATUS_SHUT) {
            $this->setError('当前活动已关闭');
            return false;
        }

        if ($activity->join_people_count >= $activity->peoples) {
            $this->setError('活动已报满,无法报名');
            return false;
        }

        if (Answer::find()->where(['question_id' => $question_id, 'user_id' => $user_id])->exists()) {
            return true;
        }

        $user = User::findOne($user_id);

        //获取问题列表
        $questionItemList = QuestionItem::findAll(['question_id' => $question_id]);
        if (3 != count($questionItemList)) {
            $this->setError('活动设置的问题不是三个');
            return false;
        }

        // 组装answerlist
        $answerItemList = [
            'q1' => [
                'question_item_id' => $post['q1']['question_item_id'],
                'question_value' => $post['q1']['question_value'],
                'question_label'=>$questionItemList['0']['label']
            ],
            'q2' => [
                'question_item_id' => $post['q2']['question_item_id'],
                'question_value' => $post['q2']['question_value'],
                'question_label'=>$questionItemList['1']['label']
            ],
            'q3' => [
                'question_item_id' => $post['q3']['question_item_id'],
                'question_value' => $post['q3']['question_value'],
                'question_label'=>$questionItemList['2']['label']
            ],
        ];

        $model = new Answer();
        $transaction = $model->getDb()->beginTransaction();

        $data = ['question_id' => $question_id, 'activity_id' => $activity_id, 'status' => Answer::STATUS_REVIEW_YET];
        $model->load($data, '');
        if (!$model->save()) {
            $transaction->rollBack();
            $this->setError('答案保存失败');
            return false;
        }

        foreach ($answerItemList as $answer) {
            $answerModel = new AnswerItem();
            $answerModel->question_id = $question_id;
            if (!$answerModel->load($answer, '') || !$answerModel->save()) {
                $transaction->rollBack();
                $this->setError('答案项保存失败');
                return false;
            }
        }

        if (0 == $user->updateCounters(['join_count' => 1])) {
            $transaction->rollBack();
            $this->setError('更新用户参加次数失败');
            return false;
        }

        if (0 == $activity->updateCounters(['join_people_count' => 1])) {
            $transaction->rollBack();
            $this->setError('更新活动参加的人数失败');
            return false;
        }

        //查询现在的活动人数是否已经报满
        $join_people_count = $activity->join_people_count;
        $is_full = $join_people_count < $activity->peoples ? Activity::IS_FULL_NO : Activity::IS_FULL_YES;

        //如果 is_full 和之前的值一样则无需要更新
        if ($is_full != $activity->is_full) {
            //尝试更新活动是否已报名完成字段, updateAll 返回受影响的行数,如果修改成功一条则返回1, 如果修改失败则标识报名失败
            if (0 == $activity->updateAll(['is_full' => $is_full])) {
                $transaction->rollBack();
                $this->setError('更新活动是否报满失败');
                return false;
            }
        }

        $transaction->commit();
        return Answer::find()
            ->select(['id', 'question_id', 'activity_id', 'user_id'])
            ->where(['id' => $model->id])
            ->with([
                'user' => function (ActiveQuery $query) {
                    $query->select(['id', 'username', 'mobile', 'wechat_id']);
                },
                'answerItemList' => function (ActiveQuery $query) {
                    $query->select(['id', 'user_id', 'question_item_id', 'question_id', 'question_label', 'question_value']);
                }
            ])
            ->asArray()
            ->one();
    }


    /**

     * 更新用户到场情况
     * @param int $id 报名的ID
     * @param int $status_arrive 0|1|2 到达的状态
     * @return array|null|\yii\db\ActiveRecord
     */
    public function updateArriveStatus($id, $status_arrive)
    {
        // 参数验证
        if (!in_array($status_arrive, [Answer::STATUS_ARRIVE_ON_TIME, Answer::STATUS_ARRIVE_LATE, Answer::STATUS_ARRIVE_YET])) {
            $this->setError('参数不正确');
            return false;
        }

        $answer = Answer::find()
            ->where(['id' => $id])
            ->with(['user', 'activity'])
            ->one();

        if (!$answer) {
            $this->setError('该报名信息不存在');
            return false;
        }
        if ($status_arrive == $answer->arrive_status) {
            return true;
        }

        if ($status_arrive == $answer->arrive_status) {
            return true;
        }

        $answer->arrive_status = $status_arrive;
        if (!$answer->save()) {
            $this->setError('更新失败');
            return false;
        }

        return true;
    }

    /**
     * 报名审核通过与否
     *
     * @param int $answer_id
     * @param int $pass_or_not
     * @param string $reject_reason
     * @return array|null|\yii\db\ActiveRecord
     * @throws \yii\db\Exception
     */
    public function reviewJoin($answer_id, $pass_or_not, $reject_reason = null)
    {
        $PASS = 1;
        $REJECT = 0;
        $halfAnHour = 1790;

        //参数校验
        if (!in_array($pass_or_not, [$REJECT, $PASS])) {
            $this->setError('请检查参数');
            return false;
        }

        //查找报名信息
        $answer = Answer::find()->where(['id' => $answer_id])->with(['user', 'activity'])->one();
        if (!$answer) {
            $this->setError('该报名信息不存在');
            return false;
        }

        if ($pass_or_not == $answer->status) {
            return true;
        }

        $user = $answer->user;
        $activity = $answer->activity;
        $user_id = $answer->user_id;
        $activity_id = $answer->activity_id;

        $transaction = $answer->getDb()->beginTransaction();
        $answer->status = $pass_or_not == $PASS ? Answer::STATUS_REVIEW_PASS : Answer::STATUS_REVIEW_REJECT;
        if (!$answer->save()) {
            $transaction->rollBack();
            $this->setError('审核报名失败');
            return false;
        } else {
            if ($pass_or_not == Answer::STATUS_REVIEW_PASS) {
                // 通过后执行的事件
                BackendEventService::filterPass($activity_id);
            } else {
                // 拒绝后执行的事件
                BackendEventService::filterReject($activity_id);
            }
        }

        //组装推送消息
        /*
         * push系统
         * 来源id  微信渠道 TUNNEL_WECHAT 1; 短信渠道 TUNNEL_SMS 2;
         * 来源类型 活动类型 FROM_ACTIVITY 1; 用户类型 FROM_USER 2;
         * 系统类型 FROM_SYSTEM  3;
         * 场地类型 FROM_SPACE 4;
         */
        $account = Account::find()->where(['user_id' => $user_id])->one();
        if (!$account) {
            $transaction->rollBack();
            $this->setError('报名没有关联微信');
            return false;
        }
        $openid = $account->client_id;

        $noti = new Noti();
        $noti->tunnel_id = Noti::TUNNEL_WECHAT;
        $noti->from_id_type = Noti::FROM_ACTIVITY;
        $noti->user_id = $user_id;
        $noti->from_id = $activity_id;

        //如果拒绝
        if ($pass_or_not == $REJECT) {
            if (!empty($reject_reason)) {
                $answer->reject_reason = $reject_reason;
                $answer->updated_at = time();
                if (!$answer->save()) {
                    $transaction->rollBack();
                    $this->setError('审核失败,拒绝理由保存失败');
                    return false;
                }
            }

            $noti->note = json_encode(NotificationTemplate::fetchFailedWechatTemplateData($openid, $user, $activity, $reject_reason));
            if (!$noti->save()) {
                $transaction->rollBack();
                $this->setError('拒绝消息保存失败');
                return false;
            }

            //提交事务
            $transaction->commit();
            //返回数据
            return Answer::find()
                ->where(['id' => $answer_id])
                ->asArray()
                ->with('answerItemList')
                ->one();
        }

        //通过的消息保存
        $noti->note = json_encode(NotificationTemplate::fetchSuccessWechatTemplateData($openid, $user, $activity));
        if (!$noti->save()) {
            $transaction->rollBack();
            $this->setError('通过的消息保存失败');
            return false;
        }

        //查找冲突的活动
        $start_time = $activity->start_time - $halfAnHour;
        $end_time = $activity->end_time + $halfAnHour;
        $conflict_activity = Activity::find()
            ->andwhere(['between', 'activity.start_time', $start_time, $end_time])
            ->orWhere(['between', 'activity.end_time', $start_time, $end_time])
            ->andWhere(['status' => Activity::STATUS_RELEASE])
            // 不包含本次活动
            ->andWhere('activity.id != ' . $activity_id)
            ->asArray()
            ->all();
        $conflict_activity_ids = [];
        foreach ($conflict_activity as $key => $value) {
            $conflict_activity_ids[$key] = $value['id'];
        }

        if (count($conflict_activity_ids) > 0) {
            $conflict_answer = Answer::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['activity_id' => $conflict_activity_ids])
                ->andWhere(['apply_status' => Answer::APPLY_STATUS_YES])
                ->andWhere(['status' => Answer::STATUS_REVIEW_YET])
                ->all();

            if (count($conflict_answer) > 0) {
                //如果存在冲突则更新其他的活动为拒绝
                foreach ($conflict_answer as $conflict_answer) {
                    $conflict_answer->status = Answer::STATUS_REVIEW_REJECT;
                    $conflict_answer->reject_reason = "由于 和其他活动时间冲突系统自动拒绝";
                    if (!$conflict_answer->save()) {
                        $transaction->rollBack();
                        $this->setError('报名冲突的活动修改失败');
                        return false;
                    }
                }
            }
        }

        $transaction->commit();
        return true;
    }

    /**
     * 取消报名, 前提是未审核状态
     *
     * @param integer $id 报名编号
     * @return bool
     * @throws \yii\db\Exception
     */
    public function cancelJoin($id)
    {
        $answer = Answer::find()
            ->with(['activity'])
            ->where(['id' => $id])
            ->one();
        if (!$answer) {
            $this->setError('报名不存在');
            return false;
        }

        if (Answer::APPLY_STATUS_YET == $answer->apply_status) {
            return true;
        }

        //如果不是未审核
        if (Answer::STATUS_REVIEW_YET != $answer->status) {
            $this->setError('您报名状态不是未审核状态，此时不能取消');
            return false;
        }

        $transaction = $answer->getDb()->beginTransaction();

        $answer->apply_status = Answer::APPLY_STATUS_YET;
        $answer->cancel_apply_time = time();

        if (!$answer->save()) {
            $transaction->rollBack();
            $this->setError('取消报名失败');
            return false;
        }

        /*
         * push系统
         * 来源id:  微信渠道 TUNNEL_WECHAT 1; 短信渠道 TUNNEL_SMS 2;
         * 来源类型: 活动类型 FROM_ACTIVITY 1; 用户类型 FROM_USER 2; 系统类型 FROM_SYSTEM  3; 场地类型 FROM_SPACE 4;
         */
        // 微信公开id 用于给指定的用户发送消息
        $account = Account::find()
            ->where(['user_id' => $answer->user_id])
            ->one();
        if (!$account) {
            $transaction->rollBack();
            $this->setError('您的帐号未关联微信');
            return false;
        }
        $openid = $account->client_id;

        $noti = new noti();
        $noti->tunnel_id = Noti::TUNNEL_WECHAT;
        $noti->user_id = $answer->user_id;
        $noti->from_id_type = Noti::FROM_ACTIVITY;
        $noti->from_id = $answer->activity_id;
        $template = NotificationTemplate::fetchUpdateCancelActivityWechatTemplateData($openid, $answer);
        if (!is_array($template)) {
            $transaction->rollBack();
            $this->setError('取消报名的消息模板格式不正确');
            return false;
        }
        $noti->note = json_encode($template);
        if (!$noti->save()) {
            $transaction->rollBack();
            $this->setError('通知添加失败');
            return false;
        }

        $transaction->commit();
        return true;
    }

    /**
     * 请假, 前提是报名通过并且活动还未开始
     *
     * @param integer $id 报名编号
     * @return array
     */
    public function leaveRequest($id)
    {
        $answer = Answer::find()
            ->where(['id' => $id])
            ->with(['user', 'user.profile', 'activity'])
            ->one();
        if (!$answer) {
            $this->setError('报名不存在');
            return false;
        }

        if (Answer::STATUS_LEAVE_YES == $answer->leave_status) {
            return true;
        }

        //如果不是通过状态则不能请假
        if (Answer::STATUS_REVIEW_PASS != $answer->status) {
            $this->setError('您报名的活动未通过，此时不能请假');
            return false;
        }

        //如果现在已经是活动开始了则无法请假
        if (time() > $answer->activity->start_time) {
            $this->setError('活动已经开始,此时不能请假');
            return false;
        }

        $answer->leave_status = Answer::STATUS_LEAVE_YES;
        $answer->leave_time = time();

        // 如果在24小时之内增加两个黄牌，在24小时之外增加一个黄牌
        $card_num = ($answer->activity->start_time - time()) < 86400 ? YellowCard::CARD_NUM_LEAVE_2 : YellowCard::CARD_NUM_LEAVE_1;
        $card_category = ($answer->activity->start_time - time()) < 86400 ? YellowCard::CARD_CATEGOTY_LEAVE_2 : YellowCard::CARD_CATEGOTY_LEAVE_1;

        $transaction = $answer->getDb()->beginTransaction();
        if (!$answer->save()) {
            $errors = $answer->getFirstErrors();
            Yii::error(array_pop($errors));
            $transaction->rollBack();
            return ['message'=> '请假失败,系统错误'];
        }

        // 判断之前有没有插入过黄牌 user_id  and  activity_id 唯一
        $yellowExists = YellowCard::find()
            ->where(
                [
                    'user_id' => $answer->user_id,
                    'activity_id' => $answer->activity_id,
                ]
            )
            ->exists();

        if ($yellowExists) {
            $transaction->commit();
            return true;
        }

        // 更新黄牌的一些数据
        $yellowCard = new YellowCard();
        $yellowCard->user_id = $answer->user_id;
        $yellowCard->username = $answer->user->username;
        $yellowCard->activity_id = $answer->activity_id;
        $yellowCard->activity_title = $answer->activity->title;
        $yellowCard->card_num = $card_num;
        $yellowCard->card_category = $card_category;
        $yellowCard->created_at = time();
        $yellowCard->status = YellowCard::STATUS_NORMAL;
        if (!$yellowCard->save()) {
            $transaction->rollBack();
            $this->setError('添加黄牌失败');
            return false;
        }

        $account = Account::find()
            ->where(['user_id' => $answer->user_id])
            ->one();
        if (!$account) {
            $transaction->rollBack();
            $this->setError('用户未关联微信');
            return false;
        }

        $openid = $account->client_id;

        $noti = new noti();
        // 来源id  微信渠道 TUNNEL_WECHAT 1; 短信渠道 TUNNEL_SMS 2;
        $noti->tunnel_id = Noti::TUNNEL_WECHAT;
        $noti->user_id = $answer->user_id;
        // 来源类型 活动类型 FROM_ACTIVITY 1; 用户类型 FROM_USER 2; 系统类型 FROM_SYSTEM  3; 场地类型 FROM_SPACE 4;
        $noti->from_id_type = Noti::FROM_ACTIVITY;
        // 活动类型id 例如 如果是活动 activity_id
        $noti->from_id = $answer->activity_id;

        $template = NotificationTemplate::fetchUpdateCreditWechatTemplateData($openid, $answer->activity, $yellowCard);
        if (!is_array($template)) {
            $transaction->rollBack();
            $this->setError('请假的消息模板不正确');
            return false;
        }

        $noti->note = json_encode($template);
        if (!$noti->save()) {
            $transaction->rollBack();
            $errors = $noti->getFirstErrors();
            Yii::error(array_pop($errors));
            $this->setError('发送通知错误');
            return false;
        }

        $transaction->commit();
        return true;
    }
}

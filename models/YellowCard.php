<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "yellow_card".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property integer $activity_id
 * @property string $activity_title
 * @property integer $card_num
 * @property string $card_category
 * @property string $created_at
 * @property integer $invalid_time
 * @property string $appeal_reason
 * @property integer $appeal_status
 * @property integer $appeal_time
 * @property string $status
 * @property integer $handle_time
 * @property integer $handle_user_id
 * @property string $handle_username
 * @property string $handle_reply
 * @property string $handle_result
 */
class YellowCard extends \yii\db\ActiveRecord
{

    // 类别

    // 取消
    const CARD_CATEGOTY_CANCEL  = 0;
    // 迟到
    const CARD_CATEGOTY_LATE  = 1;
    // 请假1 不在24小时之内一张黄牌
    const CARD_CATEGOTY_LEAVE_1  = 2;
    // 请假2 在24小时之内两张黄牌
    const CARD_CATEGOTY_LEAVE_2  = 3;
    // 爽约
    const CARD_CATEGOTY_NO  = 4;
    // 带人
    const CARD_CATEGOTY_BRING  = 5;
    // 骚扰
    const CARD_CATEGOTY_ANNOY  = 6;

    // 正常
    const STATUS_NORMAL = 0;
    // 弃用
    const STATUS_ABANDON = 1;

    // 数量

    // 黄牌数量 迟到
    const CARD_NUM_CANCEL = 0;

    // 黄牌数量 迟到
    const CARD_NUM_LATE = 1;
    // 黄牌数量 请假 在24小时之内
    // const CARD_NUM_LEAVE_IN_24_MIN = 2;
    const CARD_NUM_LEAVE_2 = 2;
    // 黄牌数量 请假 不在24小时之内
    const CARD_NUM_LEAVE_1 = 1;
    // const CARD_NUM_LEAVE_NO_24_MIN = 1;
    // 黄牌数量 爽约
    const CARD_NUM_NO = 3;
    // 黄牌数量 带人
    const CARD_NUM_BRING = 2;
    // 黄牌数量 骚扰
    const CARD_NUM_ANNOY = 1;

    const HANDLE_RESULT_NOW = 0;
    const HANDLE_RESULT_COMPLETE = 1;


    // 申诉状态 未申诉
    const APPEAL_STATUS_NO = 0;
    // 申诉状态 申诉中
    const APPEAL_STATUS_YES = 1;
    // 申诉状态 处理完成
    const APPEAL_STATUS_COMPLETE = 2;
    // 申诉状态 驳回
    const APPEAL_STATUS_REJECT = 3;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yellow_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['card_category', 'created_at','user_id', 'activity_id', 'card_num', 'invalid_time', 'appeal_status', 'appeal_time', 'handle_time', 'handle_user_id'], 'integer'],
            [['username', 'activity_title', 'appeal_reason', 'handle_username', 'handle_reply', 'handle_result'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'username' => 'Username',
            'activity_id' => 'Activity ID',
            'activity_title' => 'Activity Title',
            'card_num' => 'Card Num',
            'card_category' => 'Card Category',
            'created_at' => 'Created At',
            'invalid_time' => 'Invalid Time',
            'appeal_reason' => 'Appeal Reason',
            'appeal_status' => 'Appeal Status',
            'appeal_time' => 'Appeal Time',
            'status' => 'Status',
            'handle_time' => 'Handle Time',
            'handle_user_id' => 'Handle User ID',
            'handle_username' => 'Handle Username',
            'handle_reply' => 'Handle Reply',
            'handle_result' => 'Handle Result',
        ];
    }

    /**
     * 属于一个活动
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }
    /**
     * 属于一个用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

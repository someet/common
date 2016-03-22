<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "answer".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $activity_id
 * @property integer $user_id
 * @property integer $is_finish
 * @property integer $is_send
 * @property integer $send_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $wechat_template_push_at
 * @property integer $wechat_template_is_send
 * @property integer $wechat_template_msg_id
 * @property integer $leave_time
 *
 * @property integer $arrive_status
 * @property integer $apply_status
 * @property integer $leave_status
 * @property string $leave_msg
 * @property string $is_feedback
 */
class Answer extends \yii\db\ActiveRecord
{
    /* 未审核 */
    const STATUS_REVIEW_YET     = 10;
    /* 审核通过 */
    const STATUS_REVIEW_PASS    = 20;
    /* 审核拒绝 */
    const STATUS_REVIEW_REJECT  = 30;

    /* 短信未发送 */
    const STATUS_SMS_YET = 0;
    /* 短信发送成功 */
    const STATUS_SMS_SUCC = 1;
    /* 短信发送失败 */
    const STATUS_SMS_Fail = 2;

    /* 微信模板消息未发送 */
    const STATUS_WECHAT_TEMPLATE_YET = 0;
    /* 微信模板消息发送成功 */
    const STATUS_WECHAT_TEMPLATE_SUCC = 1;
    /* 微信模板消息发送失败 */
    const STATUS_WECHAT_TEMPLATE_Fail = 2;

    /* 参加活动的短信未发送 */
    const JOIN_NOTI_IS_SEND_YET = 0;
    /* 参加活动的短信发送成功 */
    const JOIN_NOTI_IS_SEND_SUCC = 1;
    /* 参加活动的短信发送失败 */
    const JOIN_NOTI_IS_SEND_FAIL = 2;

    /* 未到达 */
    const STATUS_ARRIVE_YET     = 0;
    /* 迟到 */
    const STATUS_ARRIVE_LATE    = 1;
    /* 准时 */
    const STATUS_ARRIVE_ON_TIME  = 2;

    /* 未请假 */
    const STATUS_LEAVE_YET    = 0;
    /* 已请假 */
    const STATUS_LEAVE_YES  = 1;

    /*  已反馈*/
    const FEEDBACK_IS    = 1;
    /* 未反馈 */
    const FEEDBACK_NO  = 0;

    /* 正常使用 */
    const APPLY_STATUS_YES = 0;
    /* 取消报名 */
    const APPLY_STATUS_YET = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id'], 'required'],
            [['leave_time', 'question_id', 'activity_id', 'user_id', 'is_finish', 'is_send', 'send_at', 'created_at', 'updated_at', 'status', 'wechat_template_push_at', 'wechat_template_is_send', 'wechat_template_msg_id', 'join_noti_is_send', 'join_noti_send_at', 'join_noti_wechat_template_push_at', 'join_noti_wechat_template_is_send', 'join_noti_wechat_template_msg_id', 'arrive_status', 'leave_status', 'apply_status'], 'integer'],
            [['leave_time'], 'default', 'value' => time()],
            [['apply_status'], 'default', 'value' => 0],
            [['question_id', 'user_id'], 'unique', 'targetAttribute' => ['question_id', 'user_id'], 'message' => 'The combination of 问题ID and 用户ID has already been taken.'],
            [['status'], 'default', 'value' => static::STATUS_REVIEW_YET]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => '问题ID',
            'activity_id' => '活动ID',
            'user_id' => '用户ID',
            'is_finish' => '0 进行中 1 已完成',
            'is_send' => '是否已经发送',
            'send_at' => '发送通知的时间',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => '0 删除 10 正常',
            'join_noti_is_send' => '参加提醒短信是否已发送',
            'join_noti_send_at' => '参加提醒短信的发送时间',
            'join_noti_wechat_template_push_at' => '参加提醒的微信模板消息发送时间',
            'join_noti_wechat_template_is_send' => '参加提醒的微信模板消息是否发送',
            'join_noti_wechat_template_msg_id' => '参加提醒的模板消息id',
            'arrive_status' => '到达的情况',
            'leave_status' => '请假状态',
            'apply_status' => '取消报名状态',
            'leave_time' => '请假时间',
            'leave_msg' => '请假内容',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => behaviors\TimestampBehavior::className(),
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->user_id = Yii::$app->user && Yii::$app->user->id > 0 ? Yii::$app->user->id : 0;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getAnswerItemList()
    {
        return $this->hasMany(AnswerItem::className(), ['question_id' => 'question_id', 'user_id' => 'user_id']);
    }

    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

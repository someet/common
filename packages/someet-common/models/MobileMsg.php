<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "mobile_msg".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $mobile_num
 * @property integer $mobile_model
 * @property integer $activity_id
 * @property string $content
 * @property integer $is_join_queue
 * @property integer $join_queue_at
 * @property integer $is_send
 * @property integer $send_at
 * @property integer $create_at
 * @property integer $msg_type
 * @property string $status
 */
class MobileMsg extends \yii\db\ActiveRecord
{

    const STATUS_SMS_SUCC = 1;
    const STATUS_SMS_YET = 0;

    // 队列发送成功
    const QUEUE_SEND_SUCC = 1;
    // 队列发送失败
    const QUEUE_SEND_YET = 0;

    // 加入队列成功
    const QUEUE_JOIN_SUCC = 1;
    // 加入队列失败
    const QUEUE_JOIN_YET = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mobile_msg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'mobile_model', 'activity_id', 'is_join_queue', 'join_queue_at', 'is_send', 'send_at', 'create_at', 'msg_type'], 'integer'],
            [['content'], 'string'],
            [['username', 'status'], 'string', 'max' => 255],
            [['mobile_num'], 'string', 'max' => 45]
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
            'mobile_num' => 'Mobile Num',
            'mobile_model' => 'Mobile  Model',
            'activity_id' => 'Activity ID',
            'content' => 'Content',
            'is_join_queue' => 'Is Join Queue',
            'join_queue_at' => 'Join Queue At',
            'is_send' => 'Is Send',
            'send_at' => 'Send At',
            'create_at' => 'Create At',
            'msg_type' => 'Msg Type',
            'status' => 'Status',
        ];
    }
}

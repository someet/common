<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "mobile_msg".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $mobile_num
 * @property integer $mobile_ model
 * @property integer $activity_id
 * @property string $content
 * @property integer $is_join_queue
 * @property integer $is_join_queue_time
 * @property integer $is_send
 * @property integer $is_send_time
 * @property integer $create_time
 * @property integer $msg_type
 * @property string $status
 */
class MobileMsg extends \yii\db\ActiveRecord
{
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
            [['user_id', 'mobile_ model', 'activity_id', 'is_join_queue', 'is_join_queue_time', 'is_send', 'is_send_time', 'create_time', 'msg_type'], 'integer'],
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
            'mobile_ model' => 'Mobile  Model',
            'activity_id' => 'Activity ID',
            'content' => 'Content',
            'is_join_queue' => 'Is Join Queue',
            'is_join_queue_time' => 'Is Join Queue Time',
            'is_send' => 'Is Send',
            'is_send_time' => 'Is Send Time',
            'create_time' => 'Create Time',
            'msg_type' => 'Msg Type',
            'status' => 'Status',
        ];
    }
}

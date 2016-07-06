<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "app_push".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $jiguang_id
 * @property string $content
 * @property string $from_type
 * @property integer $from_id
 * @property integer $from_status
 * @property integer $is_join_queue
 * @property integer $join_at
 * @property integer $is_push
 * @property integer $is_read
 * @property integer $push_at
 * @property integer $created_at
 * @property integer $status
 */
class AppPush extends \yii\db\ActiveRecord
{
    /* 已读 */
    const IS_READ_YES = 1;
    /* 未读 */
    const IS_READ_NO = 0;
    // 队列发送成功
    const QUEUE_SEND_SUCC = 1;
    // 队列还未发送
    const QUEUE_SEND_YET = 0;

    // 加入队列成功
    const QUEUE_JOIN_SUCC = 1;
    // 加入还未加入队列
    const QUEUE_JOIN_YET = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_push';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'from_id', 'from_status', 'is_join_queue', 'join_at', 'is_push', 'is_read', 'push_at', 'created_at', 'status'], 'integer'],
            [['content', 'from_type'], 'required'],
            [['jiguang_id', 'from_type'], 'string', 'max' => 64],
            [['content'], 'string', 'max' => 255],
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
            'jiguang_id' => 'Jiguang ID',
            'content' => 'Content',
            'from_type' => 'From Type',
            'from_id' => 'From ID',
            'from_status' => 'From Status',
            'is_join_queue' => 'Is Join Queue',
            'join_at' => 'Join At',
            'is_push' => 'Is Push',
            'is_read' => 'Is Read',
            'push_at' => 'Push At',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['jiguang_id']);

        return $fields;
    }
}

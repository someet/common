<?php

namespace app\models;

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
 * @property integer $is_push
 * @property integer $is_read
 * @property integer $created_at
 * @property integer $status
 */
class AppPush extends \yii\db\ActiveRecord
{
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
            [['user_id', 'from_id', 'from_status', 'is_join_queue', 'is_push', 'is_read', 'created_at', 'status'], 'integer'],
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
            'is_push' => 'Is Push',
            'is_read' => 'Is Read',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }
}

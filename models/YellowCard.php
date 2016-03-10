<?php

namespace common\models;

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
            [['user_id', 'activity_id', 'card_num', 'invalid_time', 'appeal_time', 'handle_time', 'handle_user_id'], 'integer'],
            [['username', 'created_at', 'invalid_time', 'appeal_time', 'handle_time', 'handle_username'], 'required'],
            [['username', 'activity_title', 'card_category', 'created_at', 'appeal_reason', 'status', 'handle_username', 'handle_reply', 'handle_result'], 'string', 'max' => 255]
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
            'appeal_time' => 'Appeal Time',
            'status' => 'Status',
            'handle_time' => 'Handle Time',
            'handle_user_id' => 'Handle User ID',
            'handle_username' => 'Handle Username',
            'handle_reply' => 'Handle Reply',
            'handle_result' => 'Handle Result',
        ];
    }
}

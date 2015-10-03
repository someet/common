<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "answer".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $activity_id
 * @property integer $user_id
 * @property integer $is_finish
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class Answer extends \yii\db\ActiveRecord
{
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
            [['question_id', 'activity_id', 'user_id', 'is_finish', 'created_at', 'updated_at', 'status'], 'integer'],
            [['question_id'], 'unique']
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => '0 删除 10 正常',
        ];
    }
}

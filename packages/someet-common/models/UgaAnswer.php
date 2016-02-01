<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "uga_answer".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $question_id
 * @property string $content
 * @property integer $praise
 * @property integer $status
 * @property integer $created_at
 */
class UgaAnswer extends \yii\db\ActiveRecord
{   
    /* 删除 */
    const STATUS_DELETED = 0;
    /* 正常 */
    const STATUS_NORMAL = 1;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uga_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'question_id', 'praise', 'status', 'created_at'], 'integer'],
            [['content'], 'string', 'max' => 190],
            [['user_id', 'question_id'], 'unique', 'targetAttribute' => ['user_id', 'question_id'], 'message' => 'The combination of User ID and Question ID has already been taken.']
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
            'question_id' => 'Question ID',
            'content' => 'Content',
            'praise' => 'Praise',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    // 用户
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    // 问题
    public function getQuestion()
    {
        return $this->hasMany(UgaQuestion::className(), ['id' => 'question_id']);
    }
}

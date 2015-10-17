<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "question".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property string $title
 * @property string $desc
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'title', 'desc'], 'required'],
            [['activity_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title', 'desc'], 'string', 'max' => 255],
            [['activity_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activity_id' => '活动ID',
            'title' => '问题标题',
            'desc' => '问题描述',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => '0 删除 10 草稿 20 正常',
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

    public function getQuestionItemList()
    {
        return $this->hasMany(QuestionItem::className(), ['question_id' => 'id']);
    }
}

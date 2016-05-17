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
    /* 打开 */
    const STATUS_OPEN     = 10;
    /* 关闭 */
    const STATUS_CLOSE    = 20;
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
            [['activity_id'], 'required'],
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
            'status' => '10 打开 20 关闭',
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

    /**
     * 问题项列表
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionItemList()
    {
        return $this->hasMany(QuestionItem::className(), ['question_id' => 'id']);
    }

    /**
     * 报名列表
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerList()
    {
        return $this->hasMany(Answer::className(), ['activity_id' => 'activity_id']);
    }

    /**
     * 活动
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

}

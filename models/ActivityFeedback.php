<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "activity_feedback".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property integer $user_id
 * @property integer $stars
 * @property string $feedback
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $sponsor_id
 * @property integer $sponsor_stars
 * @property integer $sponsor_stars1
 * @property integer $sponsor_stars2
 * @property integer $sponsor_stars3
 * @property integer $grade
 */
class ActivityFeedback extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'sponsor_id', 'feedback'], 'required'],
            [['activity_id', 'user_id', 'stars', 'created_at', 'updated_at', 'status', 'sponsor_stars', 'sponsor_id', 'sponsor_start1', 'sponsor_start2', 'sponsor_start3', 'grade'], 'integer'],
            ['stars', 'default', 'value' => '0'],
            ['status', 'default', 'value' => '10'],
            [['feedback'], 'string', 'max' => 255]
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
            'user_id' => '用户ID',
            'stars' => '评分, 几星',
            'feedback' => '反馈内容',
            'created_at' => '反馈时间',
            'updated_at' => '处理时间',
            'status' => '冗余扩展',
            'sponsor_id' => '发起人id',
            'sponsor_stars' => '发起人评分',
            'sponsor_start1' => '发起人的态度友好',
            'sponsor_start2' => '发起人的准备充分',
            'sponsor_start3' => '发起人的现场控制',
            'grade' => '活动内容的评价，好中差评',
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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }

    //活动报名的对象
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['user_id' => 'user_id', 'activity_id' => 'activity_id']);
    }
}


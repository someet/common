<?php

namespace someet\common\models;

use someet\common\models\Activity;
use someet\common\models\User;
use Yii;

/**
 * This is the model class for table "activity_check_in".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property integer $user_id
 * @property string $username
 * @property integer $created_at
 * @property double $longitude
 * @property double $latitude
 * @property integer $status
 * @property string $mark
 */
class ActivityCheckIn extends \yii\db\ActiveRecord
{
    /* 已签到 */
    const STATUS_CHECK_YES = 1;
    /* 未签到 */
    const STATUS_CHECK_NO = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_check_in';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'user_id', 'created_at', 'status'], 'integer'],
            [['username'], 'required'],
            [['longitude', 'latitude'], 'number'],
            [['username', 'mark'], 'string', 'max' => 60],
            ['longitude', 'default', 'value' => 0],
            ['latitude', 'default', 'value' => 0],
            ['mark', 'default', 'value' => ''],
            ['created_at', 'default', 'value' => time()],
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
            'username' => '用户名',
            'created_at' => '签到时间',
            'longitude' => '经度',
            'latitude' => '纬度',
            'status' => '签到状态 1 签到 0 未签到',
            'mark' => '签到备注',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getActivity()
    {
        return $this->hasOne(Activity::className(), ['id' => 'activity_id']);
    }
}

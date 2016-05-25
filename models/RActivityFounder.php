<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "r_activity_founder".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property integer $founder_id
 */
class RActivityFounder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_activity_founder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'activity_id', 'founder_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activity_id' => 'Activity ID',
            'founder_id' => 'Founder ID',
        ];
    }

    /**
     * 发起人信息
     * @return 发起人信息
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'founder_id']);
    }
}

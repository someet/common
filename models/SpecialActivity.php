<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "special_activity".
 *
 * @property integer $id
 * @property integer $special_id
 * @property integer $activity_id
 */
class SpecialActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'special_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['special_id', 'activity_id'], 'required'],
            [['special_id', 'activity_id'], 'integer'],
            [['special_id', 'activity_id'], 'unique', 'targetAttribute' => ['special_id', 'activity_id'], 'message' => 'The combination of 专题ID and 活动ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'special_id' => '专题ID',
            'activity_id' => '活动ID',
        ];
    }
}

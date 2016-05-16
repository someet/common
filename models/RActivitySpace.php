<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "r_activity_space".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property integer $space_spot_id
 * @property integer $space_section_id
 */
class RActivitySpace extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_activity_space';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'space_spot_id', 'space_section_id'], 'integer']
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
            'space_spot_id' => 'Space Spot ID',
            'space_section_id' => 'Space Section ID',
        ];
    }
}

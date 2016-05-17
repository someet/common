<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "r_spot_device".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $device_id
 */
class RSpotDevice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_spot_device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'device_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '空间编号',
            'device_id' => '设备编号',
        ];
    }
}

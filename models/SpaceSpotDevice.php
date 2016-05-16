<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "space_spot_device".
 *
 * @property integer $id
 * @property string $name
 * @property string $icon
 * @property integer $display_order
 * @property integer $status
 */
class SpaceSpotDevice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'space_spot_device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['display_order', 'status'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['icon'], 'string', 'max' => 180],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'icon' => '图标',
            'display_order' => '排序',
            'status' => '状态 0 未设置 10 可用 20 不可用',
        ];
    }

    /**
     * 获取对应场地
     * @return int|string
     */
    public function getSpots()
    {
        return $this->hasMany(SpaceSpot::className(), ['id' => 'spot_id'])
            ->viaTable('r_spot_device', ['device_id' => 'id']);
    }
}

<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "space_type".
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $city
 * @property string $name
 * @property integer $display_order
 * @property integer $status
 */
class SpaceType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'space_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['display_order', 'status', 'city_id'], 'integer'],
            [['city'], 'string', 'max' => 60],
            [['name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => '城市编号',
            'city' => '城市',
            'name' => '类型名称，例如酒吧，咖啡厅',
            'display_order' => '显示排序',
            'status' => '状态 0 默认值 10 可用 20 删除',
        ];
    }


    /**
     * 获取对应场地
     * @return int|string
     */
    public function getSpots()
    {
        return $this->hasMany(SpaceSpot::className(), ['type_id' => 'id']);
    }
}

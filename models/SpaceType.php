<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "space_type".
 *
 * @property integer $id
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
            [['display_order', 'status'], 'integer'],
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

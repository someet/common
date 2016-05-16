<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "space_section".
 *
 * @property integer $id
 * @property string $name
 * @property integer $spot_id
 * @property integer $people
 * @property integer $status
 */
class SpaceSection extends \yii\db\ActiveRecord
{
    const STATUS_DELETE = 0;
    const STATUS_NORMAL = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'space_section';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['spot_id', 'people', 'status'], 'integer'],
            [['status'], 'default', 'value' => 10],
            [['name'], 'string', 'max' => 180],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '空间名称',
            'spot_id' => '地点编号',
            'people' => '推荐人数',
            'status' => '状态 0 删除 10 正常',
        ];
    }

    // 场地数
    public function getSpots()
    {
        return $this->hasMany(SpaceSpot::className(), ['id' => 'spot_id']);
    }

}

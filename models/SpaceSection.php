<?php

namespace someet\common\models;

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
            'status' => '状态',
        ];
    }
}

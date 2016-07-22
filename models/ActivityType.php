<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "activity_type".
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $city
 * @property string $name
 * @property integer $display_order
 * @property integer $status
 * @property string $img
 */
class ActivityType extends \yii\db\ActiveRecord
{
    /* 删除 */
    const STATUS_DELETE     = 0;
    /* 正常 */
    const STATUS_NORMAL    = 10;
    /* 隐藏 */
    const STATUS_HIDDEN  = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_type';
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
            [['img'], 'string', 'max' => 255],
            ['display_order', 'default', 'value' => '99'],
            ['status', 'default', 'value' => '10'],
            [
                'name',
                'string',
                'min' => 2,
                'max' => 60,
                'tooLong' => '{attribute}长度不得超过60个字符',
                'tooShort' => '{attribute}最少含有2个字符',
            ],
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
            'name' => '名称',
            'display_order' => '显示顺序',
            'status' => 'Status',
            'img' => '图片',
        ];
    }

    /**
     * 活动列表
     * @return int|string
     */
    public function getActivities()
    {
        return $this->hasMany(Activity::className(), ['type_id' => 'id']);
    }
}

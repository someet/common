<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "activity_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $display_order
 * @property integer $status
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
            ['name', 'unique', 'message' => '{attribute}已存在'],
            [['display_order', 'status'], 'integer'],
            ['display_order', 'default', 'value' => '99'],
            ['status', 'default', 'value' => '10'],
            [
                'name',
                'string',
                'min' => 2,
                'max' => 10,
                'tooLong' => '{attribute}长度不得超过10个字符',
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
            'name' => '名称',
            'display_order' => '显示顺序',
            'status' => 'Status',
        ];
    }

    /**
     * 获取对应活动
     * @return int|string
     */
    public function getActivities()
    {
        return $this->hasMany(Activity::className(), ['type_id' => 'id']);
    }
}

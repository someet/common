<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "activity_type".
 *
 * @property integer $id
 * @property string $name
 * @property integer $displayorder
 * @property integer $status
 */
class ActivityType extends \yii\db\ActiveRecord
{
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
            [['displayorder', 'status'], 'integer'],
            ['displayorder', 'default', 'value' => '99'],
            ['status', 'default', 'value' => '10'],
            [
                'name',
                'string',
                'min' => 2,
                'max' => 255,
                'tooLong' => '{attribute}长度不得超过255个字符',
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
            'displayorder' => '显示顺序',
            'status' => 'Status',
        ];
    }
}

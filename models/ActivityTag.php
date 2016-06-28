<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "activity_tag".
 *
 * @property integer $id
 * @property integer $frequency
 * @property string $name
 * @property integer $status
 */
class ActivityTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique', 'message' => '{attribute}已存在'],
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
            'name' => '标签名称',
            'frequency' => '频率',
            'status' => '冗余扩展',
        ];
    }
}

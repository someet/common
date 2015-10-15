<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "activity_tag".
 *
 * @property integer $id
 * @property string $label
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
            [['label'], 'required'],
            [['status'], 'integer'],
            [['label'], 'string', 'max' => 255],
            [['label'], 'unique', 'message' => '{attribute}已存在'],
            [
                'label',
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
            'label' => '标签',
            'status' => '冗余扩展',
        ];
    }
}

<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "sms_template".
 *
 * @property integer $id
 * @property string $name
 * @property string $template
 * @property integer $status
 */
class SmsTemplate extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'template'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['template'], 'string', 'max' => 255],
            ['status', 'default', 'value' => '10'],
            [
                'name',
                'string',
                'min' => 2,
                'max' => 10,
                'tooLong' => '{attribute}长度不得超过10个字符',
                'tooShort' => '{attribute}最少含有2个字符',
            ],
            [
                'template',
                'string',
                'min' => 10,
                'max' => 255,
                'tooLong' => '{attribute}长度不得超过255个字符',
                'tooShort' => '{attribute}最少含有10个字符',
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
            'name' => '模板名称, 例如, 审核通过, 审核失败, 请假通知',
            'template' => 'Template',
            'status' => '冗余扩展',
        ];
    }
}

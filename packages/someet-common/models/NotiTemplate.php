<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "noti_template".
 *
 * @property integer $id
 * @property string $name
 * @property string $template
 * @property integer $type_id
 * @property string $mark
 * @property integer $status
 */
class NotiTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'noti_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'template'], 'required'],
            [['type_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['template', 'mark'], 'string', 'max' => 180]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'template' => 'Template',
            'type_id' => 'Type ID',
            'mark' => 'Mark',
            'status' => 'Status',
        ];
    }
}

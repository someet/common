<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "noti_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $mark
 * @property integer $status
 */
class NotiType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'noti_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['mark'], 'string', 'max' => 180]
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
            'mark' => 'Mark',
            'status' => 'Status',
        ];
    }
}

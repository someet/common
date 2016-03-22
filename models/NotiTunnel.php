<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "noti_tunnel".
 *
 * @property integer $id
 * @property string $name
 * @property string $mark
 * @property integer $status
 */
class NotiTunnel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'noti_tunnel';
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

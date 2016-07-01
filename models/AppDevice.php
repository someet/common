<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "app_device".
 *
 * @property integer $id
 * @property string $platform
 * @property string $device_id
 * @property string $jiguang_id
 * @property string $alias_id
 * @property string $apple_token
 * @property string $app_name
 * @property string $app_version
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $user_id
 * @property integer $status
 * @property integer $jsd_show
 * @property string $device_model
 * @property integer $push_provider
 */
class AppDevice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'user_id', 'status', 'jsd_show', 'push_provider'], 'integer'],
            [['platform', 'device_id', 'jiguang_id', 'alias_id', 'apple_token', 'app_name', 'app_version'], 'string', 'max' => 64],
            [['device_model'], 'string', 'max' => 20],
            [['device_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform' => 'ios/android',
            'device_id' => '设备标识',
            'jiguang_id' => '极光id',
            'alias_id' => '极光别名，安卓同device_id, ios为设置极光推送别名',
            'apple_token' => '苹果token',
            'app_name' => 'app名称',
            'app_version' => 'app版本',
            'created_at' => '创建时间',
            'updated_at' => '上次访问时间',
            'user_id' => 'User ID',
            'status' => '1:开启 2:关闭',
            'jsd_show' => '1:显示 2:不显示',
            'device_model' => '设备型号',
            'push_provider' => '推送服务商 1:jpush',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => behaviors\TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * 用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

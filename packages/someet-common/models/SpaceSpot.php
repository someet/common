<?php

namespace common\models;

use someet\common\models\User;
use Yii;

/**
 * This is the model class for table "space_spot".
 *
 * @property integer $id
 * @property string $name
 * @property string $area
 * @property string $address
 * @property integer $type_id
 * @property string $image
 * @property string $router
 * @property string $map_pic
 * @property string $detail
 * @property string $contact
 * @property string $base_fee
 * @property integer $principal
 * @property string $logo
 * @property string $owner
 * @property string $open_time
 * @property double $longitude
 * @property double $latitude
 * @property integer $status
 */
class SpaceSpot extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'space_spot';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'principal', 'status'], 'integer'],
            [['detail'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['name', 'area', 'address', 'image', 'router', 'map_pic', 'contact', 'base_fee', 'owner', 'open_time'], 'string', 'max' => 180],
            [['logo'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '地点名称',
            'area' => '商圈',
            'address' => '详细地址',
            'type_id' => '场地分类编号',
            'image' => '图片',
            'router' => '到达路线信息，例如公交地铁',
            'map_pic' => '地图图片',
            'detail' => '地点详情',
            'contact' => '场地负责人联系方式',
            'base_fee' => '最低消费',
            'principal' => '官方负责人',
            'logo' => 'Logo',
            'owner' => '场地所有者名称/公司',
            'open_time' => '开放时间',
            'longitude' => '经度',
            'latitude' => '纬度',
            'status' => '状态',
        ];
    }

    // 活动的类型
    public function getType()
    {
        return $this->hasOne(SpaceType::className(), ['id' => 'type_id']);
    }

    //设备
    public function getDevices()
    {
        return $this->hasMany(SpaceSpotDevice::className(), ['id' => 'device_id'])
            ->viaTable('r_spot_device', ['spot_id' => 'id']);
    }

}

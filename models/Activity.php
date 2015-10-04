<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "activity".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $title
 * @property string $desc
 * @property string $poster
 * @property integer $week
 * @property integer $starttime
 * @property integer $endtime
 * @property string $area
 * @property string $address
 * @property string $details
 * @property string $groupcode
 * @property double $longitude
 * @property double $latitude
 * @property integer $cost
 * @property integer $peoples
 * @property integer $isvolume
 * @property integer $isdigest
 * @property integer $responsi
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $status
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'title', 'desc', 'poster', 'area', 'address', 'details' ], 'required'],
            [['type_id', 'week', 'starttime', 'endtime', 'cost', 'peoples', 'isvolume', 'isdigest', 'responsi', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [['details'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['longitude', 'latitude'], 'default', 'value' => 0],
            ['groupcode', 'default', 'value' => '0'],
            [['title'], 'string', 'max' => 80],
            [['desc', 'poster', 'address'], 'string', 'max' => 255],
            [['area'], 'string', 'max' => 10],
            [['groupcode'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => '分类ID',
            'title' => '标题',
            'desc' => '描述',
            'poster' => '海报',
            'week' => '星期 按照活动时间自动计算',
            'starttime' => '活动开始时间',
            'endtime' => '活动结束时间',
            'area' => '范围, 比如雍和宫',
            'address' => '活动详细地址',
            'details' => '活动详情',
            'groupcode' => '群二维码',
            'longitude' => '经度',
            'latitude' => '纬度',
            'cost' => '0 免费 大于0 则收费',
            'peoples' => '0 不限制 >1 则为限制人数',
            'isvolume' => '0 非系列 1 系列活动',
            'isdigest' => '0 非精华 1 精华',
            'responsi' => '负责人 0为未设置',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'status' => '20 删除 40 草稿 60 审核不通过 80 审核通过 100 进行中 120 已结束 ',
        ];
    }

    public function getType()
    {
        return $this->hasOne(ActivityType::className(), ['id' => 'type_id']);
    }
}

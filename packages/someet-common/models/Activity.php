<?php

namespace someet\common\models;

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
 * @property integer $start_time
 * @property integer $end_time
 * @property string $area
 * @property string $address
 * @property string $details
 * @property string $group_code
 * @property double $longitude
 * @property double $latitude
 * @property integer $cost
 * @property string $cost_list
 * @property integer $peoples
 * @property integer $is_volume
 * @property integer $is_digest
 * @property integer $is_top
 * @property integer $principal
 * @property integer $review
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $status
 */
class Activity extends \yii\db\ActiveRecord
{

    // 标签名, 用于标签行为使用此属性
    public $tagNames;
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
            [['type_id', 'week', 'start_time', 'end_time', 'cost', 'peoples', 'is_volume', 'is_digest', 'is_top', 'principal', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [['details', 'review'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['longitude', 'latitude'], 'default', 'value' => 0],
            ['group_code', 'default', 'value' => '0'],
            [['title'], 'string', 'max' => 80],
            [['desc', 'poster', 'group_code', 'address', 'cost_list', 'tagNames'], 'string', 'max' => 255],
            [['area'], 'string', 'max' => 10],
            [['tagNames'], 'safe'],
            [['status'], 'default', 'value' => 10]
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
            'start_time' => '活动开始时间',
            'end_time' => '活动结束时间',
            'area' => '范围, 比如雍和宫',
            'address' => '活动详细地址',
            'details' => '活动详情',
            'group_code' => '群二维码',
            'longitude' => '经度',
            'latitude' => '纬度',
            'cost' => '0 免费 大于0 则收费',
            'peoples' => '0 不限制 >1 则为限制人数',
            'is_volume' => '0 非系列 1 系列活动',
            'is_digest' => '0 非精华 1 精华',
            'is_top' => '0 正常 1 置顶',
            'principal' => '负责人 0为未设置',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'status' => '20 删除 40 草稿 60 审核不通过 80 审核通过 100 进行中 120 已结束 ',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => behaviors\TimestampBehavior::className(),
            ],
            behaviors\TaggableBehavior::className(),
        ];
    }

    // 活动标签
    public function getTags()
    {
        return $this->hasMany(ActivityTag::className(), ['id' => 'tag_id'])->viaTable('r_tag_activity', ['activity_id' => 'id']);
    }

    // 活动的类型
    public function getType()
    {
        return $this->hasOne(ActivityType::className(), ['id' => 'type_id']);
    }

    // 活动反馈列表
    public function getFeedbackList()
    {
        return $this->hasOne(ActivityFeedback::className(), ['activity_id' => 'id']);
    }
}

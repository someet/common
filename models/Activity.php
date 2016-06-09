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
 * @property integer $edit_status
 * @property integer $content
 * @property integer $display_order
 * @property string $field1
 * @property string $field2
 * @property string $field3
 * @property string $field4
 * @property string $field5
 * @property string $field6
 * @property string $field7
 * @property string $field8
 * @property integer $co_founder1
 * @property integer $co_founder2
 * @property integer $co_founder3
 * @property integer $co_founder4
 * @property integer $is_full
 * @property integer $join_people_count
 * @property integer $pma_type
 * @property integer $space_spot_id
 * @property integer $ideal_number
 * @property integer $ideal_number_limit
 */
class Activity extends \yii\db\ActiveRecord
{

    /* 删除 */
    const STATUS_DELETE   = 0;
    /* 发起人创建的活动的草稿 */
    const STATUS_FOUNDER_DRAFT = 5;    
    /* 草稿 */
    const STATUS_DRAFT    = 10;
    /* 预发布 */
    const STATUS_PREVENT  = 15;
    /* 发布 */
    const STATUS_RELEASE  = 20;
    /* 关闭 */
    const STATUS_SHUT  = 30;
    /* 取消 */
    const STATUS_CANCEL = 40;
    /* 待审核 */
    const STATUS_CHECK = 8;
    /* 好评 */
    const GOOD_SCORE = 1;
    /* 中评 */
    const MIDDLE_SCORE = 2;
    /* 差评 */
    const BAD_SCORE = 3;

    /* 报名已满 */
    const IS_FULL_YES = 1;
    /* 报名未满 */
    const IS_FULL_NO = 0;

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
            [['title'], 'required'],
            [['type_id', 'week', 'start_time', 'end_time', 'cost', 'peoples', 'is_volume', 'is_digest', 'is_top', 'principal', 'pma_type','created_at', 'created_by', 'updated_at', 'updated_by', 'status', 'edit_status', 'display_order', 'co_founder1', 'co_founder2', 'co_founder3', 'co_founder4', 'is_full', 'join_people_count','space_spot_id','ideal_number','ideal_number_limit'], 'integer'],
            [['details', 'review', 'content', 'field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7', 'field8'], 'string'],
            [['longitude', 'latitude'], 'number'],
            [['longitude', 'latitude','pma_type'], 'default', 'value' => 0],
            [['ideal_number','ideal_number_limit','peoples'], 'default', 'value' => 10],
            ['group_code', 'default', 'value' => '0'],
            [['area','desc','address','details'], 'default', 'value' => '0'],
            ['poster', 'default', 'value' => 'http://7xn8h3.com2.z0.glb.qiniucdn.com/FtlMz_y5Pk8xMEPQCw5MGKCRuGxe'],
            ['start_time', 'default', 'value' => time()],
            ['end_time', 'default', 'value' => time()+7200],
            [['title'], 'string', 'max' => 80],
            [['address_assign','desc', 'poster', 'group_code', 'address', 'cost_list', 'tagNames'], 'string', 'max' => 255],
            [['area'], 'string', 'max' => 10],
            [['tagNames'], 'safe'],
            [['status'], 'default', 'value' => 10],
            [['display_order'], 'default', 'value' => 99]
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['edit_status'], $fields['is_top'], $fields['is_digest'], $fields['is_volume'], $fields['week']);

        return $fields;
    }

    public function extraFields()
    {
        return ['type', 'user','pma', 'spot', 'founders', 'profile' => function() {
            if ($this->user) {
                return $this->user->profile;
            }
            return null;
        }];
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
            'pma_type' => 'pma类型',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'status' => '0 删除 10 草稿 20 发布',
            'edit_status' => '扩展字段, 前端自定义状态',
            'content' => '文案',
            'display_order' => '显示排序',
            'field1' => '扩展字段1',
            'field2' => '扩展字段2',
            'field3' => '扩展字段3',
            'field4' => '扩展字段4',
            'field5' => '扩展字段5',
            'field6' => '扩展字段6',
            'field7' => '扩展字段7',
            'field8' => '扩展字段8',
            'co_founder1' => '联合创始人1',
            'co_founder2' => '联合创始人2',
            'co_founder3' => '联合创始人3',
            'co_founder4' => '联合创始人4',
            'is_full' => '是否报满',
            'join_people_count' => '已报名的人数',
            'space_spot_id' => '场地id',
            'space_section_id' => '空间id',
            'ideal_number' => '理想人数',
            'ideal_number_limit' => '理想人数限制',
            'address_assign' => '场地是否分配',
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
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->updated_by = Yii::$app->user->id;
            }
            return true;
        } else {
            return false;
        }
    }

    // 活动标签
    public function getTags()
    {
        return $this->hasMany(ActivityTag::className(), ['id' => 'tag_id'])->viaTable('r_tag_activity', ['activity_id' => 'id']);
    }

    // PMA
    public function getPma()
    {
        return $this->hasOne(User::className(), ['id' => 'principal']);
    }

    // DTS
    public function getDts()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    // 发起人
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }


    // 联合发起人1
    public function getCofounder1()
    {
        return $this->hasOne(User::className(), ['id' => 'co_founder1']);
    }

    // 联合发起人2
    public function getCofounder2()
    {
        return $this->hasOne(User::className(), ['id' => 'co_founder2']);
    }
    // 联合发起人3
    public function getCofounder3()
    {
        return $this->hasOne(User::className(), ['id' => 'co_founder3']);
    }
    // 联合发起人4
    public function getCofounder4()
    {
        return $this->hasOne(User::className(), ['id' => 'co_founder4']);
    }

    /**
     * 类型
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ActivityType::className(), ['id' => 'type_id']);
    }
    
    // 活动的场地
    public function getSpace()
    {
        return $this->hasOne(SpaceSpot::className(), ['id' => 'space_spot_id']);
    }

    /**
     * 活动问题
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['activity_id' => 'id']);
    }

    /**
     * 报名列表
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerList()
    {
        return $this->hasMany(Answer::className(), ['activity_id' => 'id']);
    }

    /**
     * 反馈列表
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbackList()
    {
        return $this->hasMany(ActivityFeedback::className(), ['activity_id' => 'id']);
    }

    /**
     * 黄牌列表
     * @return \yii\db\ActiveQuery
     */
    public function getYellowCardList()
    {
        return $this->hasMany(YellowCard::className(), ['activity_id' => 'id']);
    }

    /**
     * 签到列表
     * @return \yii\db\ActiveQuery
     */
    public function getCheckInList()
    {
        return $this->hasMany(ActivityCheckIn::className(), ['activity_id' => 'id']);
    }

    /**
     * 场地
     * @return \yii\db\ActiveQuery
     */
    public function getSpot()
    {
        return $this->hasOne(SpaceSpot::className(), ['id' => 'space_spot_id']);
    }

    public function getFounders()
    {
        return $this->hasMany(User::className(), ['id' => 'founder_id'])->viaTable('r_activity_founder', ['activity_id' => 'id']);
    }
}

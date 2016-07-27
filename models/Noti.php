<?php

namespace someet\common\models;

use someet\common\models\User;
use Yii;

/**
 * This is the model class for table "noti".
 *
 * @property integer $id
 * @property integer $tunnel_id
 * @property integer $type_id
 * @property integer $user_id
 * @property integer $new
 * @property string $author
 * @property integer $author_id
 * @property string $note
 * @property integer $from_id
 * @property integer $from_id_type
 * @property integer $from_num
 * @property integer $sended_at
 * @property integer $callback_id
 * @property string $callback_msg
 * @property integer $callback_status
 * @property integer $created_at
 * @property integer $timing
 * @property integer $work_on
 * @property integer $work_off
 */
class Noti extends \yii\db\ActiveRecord
{

    /* 微信渠道 */
    const TUNNEL_WECHAT   = 1;
    /* 短信渠道 */
    const TUNNEL_SMS    = 2;
    /* app渠道 */
    const TUNNEL_APP    = 3;
    /* 站内信渠道 */
    const TUNNEL_MSG    = 4;

    /* 活动类型 */
    const FROM_ACTIVITY    = 1;
    /* 用户类型 */
    const FROM_USER    = 2;
    /* 系统类型 */
    const FROM_SYSTEM    = 3;
    /* 场地类型 */
    const FROM_SPACE    = 4;

    /* 通知发送状态成功 */
    const CALLBACK_STATUS_SUCCESS    = 10;
    /* 通知发送状态失败 */
    const CALLBACK_STATUS_FAILURE    = 20;

    /* 未进队列 */
    const IN_TUBE_YET = 0;
    /* 进队列成功 */
    const IN_TUBE_YES = 1;
    /* 进队列失败 */
    const IN_TUBE_FAIL = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'noti';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tunnel_id', 'type_id', 'user_id', 'new', 'author_id', 'from_id', 'from_id_type', 'from_num', 'sended_at', 'callback_id', 'callback_status', 'created_at', 'timing', 'work_on', 'work_off'], 'integer'],
            ['author', 'default', 'value' => 0],
            [['author', 'note'], 'required'],
            // [['author'], 'string', 'max' => 60],
            // [['note', 'callback_msg'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tunnel_id' => 'Tunnel ID',
            'type_id' => 'Type ID',
            'user_id' => 'User ID',
            'new' => 'New',
            'author' => 'Author',
            'author_id' => 'Author ID',
            'note' => 'Note',
            'from_id' => 'From ID',
            'from_id_type' => 'From Id Type',
            'from_num' => 'From Num',
            'sended_at' => 'Sended At',
            'callback_id' => 'Callback ID',
            'callback_msg' => 'Callback Msg',
            'callback_status' => 'Callback Status',
            'created_at' => 'Created At',
            'timing' => 'Timing',
            'work_on' => 'Work On',
            'work_off' => 'Work Off',
        ];
    }

    // 被通知的用户
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    //报名通知的发送情况
    public function getAnswer()
    {
        return $this->hasMany(Answer::classname(), ['activity_id' => 'from_id', 'user_id' => 'user_id']);
    }

}

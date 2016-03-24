<?php

namespace common\models;

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
            [['author', 'note'], 'required'],
            [['author'], 'string', 'max' => 60],
            [['note', 'callback_msg'], 'string', 'max' => 180]
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
}

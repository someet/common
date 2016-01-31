<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "uga_question".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $content
 * @property integer $is_official
 * @property integer $praise_num
 * @property integer $anwers_num
 * @property integer $created_at
 * @property integer $status
 */
class UgaQuestion extends \yii\db\ActiveRecord
{
    
    /* 是官方  */
    const OFFICIAL_IS = 10;
     /* 民间 */
    const OFFICIAL_NO = 0;
     /* 民间公开库 */
    const FOLK_PUBLICK = 1;
     /* 民间私有库 */
    const FOLK_PRIVATE = 2;
    /* 删除 */
    const STATUS_DELETED = 0;
    /* 正常 */
    const STATUS_NORMAL = 1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uga_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'is_official', 'praise_num', 'anwers_num', 'created_at', 'status'], 'integer'],
            [['content'], 'string', 'max' => 190]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'content' => 'Content',
            'is_official' => 'Is Official',
            'praise_num' => 'Praise Num',
            'anwers_num' => 'Anwers Num',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }

    // 回答列表
    public function getAnswerList()
    {
        return $this->hasMany(UgaAnswer::className(), ['question_id' => 'id']);
    }

    // 用户
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "uga_praise".
 *
 * @property integer $id
 * @property integer $answer_id
 * @property integer $user_id
 */
class UgaPraise extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uga_praise';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer_id', 'user_id'], 'integer'],
            [['answer_id', 'user_id'], 'unique', 'targetAttribute' => ['answer_id', 'user_id'], 'message' => 'The combination of Answer ID and User ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'answer_id' => 'Answer ID',
            'user_id' => 'User ID',
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

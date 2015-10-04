<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "answer_item".
 *
 * @property integer $id
 * @property integer $question_item_id
 * @property integer $question_id
 * @property string $question_label
 * @property string $question_value
 * @property integer $status
 */
class AnswerItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answer_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_item_id', 'question_id', 'question_label', 'question_value'], 'required'],
            [['question_item_id', 'question_id', 'status'], 'integer'],
            [['question_label', 'question_value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_item_id' => '问题项ID',
            'question_id' => '问题项ID',
            'question_label' => '问题标题, 为了保留历史记录',
            'question_value' => '问题的值',
            'status' => '冗余扩展',
        ];
    }
}

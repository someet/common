<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "question_item".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $type_id
 * @property string $label
 * @property string $desc
 * @property string $extra
 * @property integer $display_order
 * @property integer $status
 */
class QuestionItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'label'], 'required'],
            ['desc', 'default', 'value' => '0'],
            ['type_id', 'default', 'value' => '0'],
            [['question_id', 'type_id', 'display_order', 'status'], 'integer'],
            [['label', 'desc', 'extra'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => '问题ID',
            'type_id' => '类型ID',
            'label' => '标题',
            'desc' => '简介',
            'extra' => '多选框, 下拉菜单这种字段的 多个值, 以分号分割的字符串信息',
            'display_order' => '问题项排序',
            'status' => '冗余扩展',
        ];
    }

    /**
     * 活动问题
     * @return \yii\db\ActiveQuery
     */
    public function getFeedbackList()
    {
        return $this->hasMany(Question::className(), ['id', 'question_id']);
    }
}

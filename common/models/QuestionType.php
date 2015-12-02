<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "question_type".
 *
 * @property integer $id
 * @property string $field_name
 * @property string $field_lib
 * @property integer $field_type
 * @property integer $check_type
 * @property integer $status
 */
class QuestionType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_name', 'field_lib', 'field_type', 'check_type'], 'required'],
            [['field_type', 'check_type', 'status'], 'integer'],
            [['field_name', 'field_lib'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_name' => '字段名称',
            'field_lib' => 'html模板信息',
            'field_type' => '可用的全部字段类型 (输入框, 文本域, 下拉菜单, 上传框等)
text, number, textarea, dropdownlist, radio, checkbox, date, file, img',
            'check_type' => '标识 该字段类型 用什么方式验证, 比如 数字, 邮箱, 字符串, 单选, 多选, 文件上传. 用于表单的字段验证',
            'status' => '冗余扩展',
        ];
    }
}

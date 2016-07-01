<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "app_push".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $jiguang_id
 * @property string $content
 * @property string $from_type
 * @property integer $from_id
 * @property integer $from_status
 * @property integer $is_read
 * @property integer $created_at
 * @property integer $status
 */
class AppPush extends \yii\db\ActiveRecord
{
    /* 已读 */
    const IS_READ_YES = 1;
    /* 未读 */
    const IS_READ_NO = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_push';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'from_id', 'from_status', 'is_read', 'created_at', 'status'], 'integer'],
            [['content', 'from_type'], 'required'],
            [['jiguang_id', 'from_type'], 'string', 'max' => 64],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户编号',
            'jiguang_id' => '极光编号',
            'content' => '推送内容',
            'from_type' => '内容来源类型，例如活动，用户等',
            'from_id' => '来源编号，如果是活动的话，则是活动编号',
            'from_status' => '来源的状态 例如活动的通过，不通过状态',
            'is_read' => '是否已读 0 未读 1 已读',
            'created_at' => '推送时间',
            'status' => '状态 ',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['jiguang_id']);

        return $fields;
    }

}

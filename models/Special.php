<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "special".
 *
 * @property integer $id
 * @property string $title
 * @property string $desc
 * @property string $poster
 * @property integer $displayorder
 * @property integer $sharetimes
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 */
class Special extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_DRAFT   = 10;
    const STATUS_ACTIVE  = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'special';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'poster'], 'required'],
            ['displayorder', 'default', 'value' => '99'],
            ['status', 'default', 'value' => '10'],
            [['displayorder', 'sharetimes', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title', 'desc', 'poster'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'desc' => '描述',
            'poster' => '海报',
            'displayorder' => '显示排序',
            'sharetimes' => '分享次数',
            'created_at' => '制作时间',
            'updated_at' => 'Updated At',
            'status' => '0 删除 10 草稿 20 正常',
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
}

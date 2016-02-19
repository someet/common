<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "share".
 *
 * @property integer $id
 * @property integer $page_id
 * @property integer $user_id
 * @property string $title
 * @property string $desc
 * @property string $link
 * @property string $imgurl
 * @property integer $created_at
 * @property integer $status
 */
class Share extends \yii\db\ActiveRecord
{
    /* 未启用 */
    const STATUS_DISABLE = 0;
    
    /* 启用 */
    const STATUS_ENABLE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'share';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'user_id', 'created_at', 'status'], 'integer'],
            [['title', 'desc', 'link', 'imgurl'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page_id' => 'Page ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'desc' => 'Desc',
            'link' => 'Link',
            'imgurl' => 'Imgurl',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }
}

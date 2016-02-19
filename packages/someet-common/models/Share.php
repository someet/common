<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "share".
 *
 * @property integer $id
 * @property string $title
 * @property string $desc
 * @property string $link
 * @property string $imgurl
 */
class Share extends \yii\db\ActiveRecord
{
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
            [['id'], 'required'],
            [['id'], 'integer'],
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
            'title' => 'Title',
            'desc' => 'Desc',
            'link' => 'Link',
            'imgurl' => 'Imgurl',
        ];
    }
}

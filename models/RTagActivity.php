<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_tag_activity".
 *
 * @property integer $id
 * @property integer $activity_id
 * @property integer $tag_id
 */
class RTagActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_tag_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'tag_id'], 'required'],
            [['activity_id', 'tag_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'activity_id' => '活动ID',
            'tag_id' => '标签ID',
        ];
    }
}

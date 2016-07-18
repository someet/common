<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $city
 * @property integer $city_id
 * @property integer $status
 */
class City extends \yii\db\ActiveRecord
{
    /* 显示 */
    const STATUS_SHOW = 1;
    /* 隐藏 */
    const STATUS_HIDDEN = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'city_id'], 'required'],
            [['city_id', 'status'], 'integer'],
            [['city'], 'string', 'max' => 60],
            [['city'], 'unique'],
            [['city_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city' => 'City',
            'city_id' => 'City ID',
            'status' => 'Status',
        ];
    }
}

<?php

namespace someet\common\models;

use Yii;
use dektrium\user\models\Profile as BaseProfile;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string $name
 * @property string $public_email
 * @property string $gravatar_email
 * @property string $gravatar_id
 * @property string $location
 * @property string $website
 * @property string $bio
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $headimgurl
 * @property integer $sex
 * @property integer $birth_year
 * @property integer $birth_month
 * @property integer $birth_day
 * @property string $constellation
 * @property string $zodiac
 * @property string $company
 * @property string $education
 * @property string $occupation
 * @property string $position
 * @property string $affective_status
 * @property string $lookingfor
 * @property string $blood_type
 * @property string $height
 * @property string $weight
 * @property string $interest
 * @property string $from
 * @property string $want
 * @property string $recommand
 *
 * @property User $user
 */
class Profile extends BaseProfile
{
    public $from;
    public $want;
    public $recommand;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['sex', 'birth_year', 'birth_month', 'birth_day'], 'integer'],
            [['country', 'province', 'city', 'headimgurl', 'constellation', 'zodiac', 'company', 'education', 'occupation', 'position', 'affective_status', 'lookingfor', 'blood_type', 'height', 'weight', 'interest', 'from', 'want', 'recommand'], 'string', 'max' => 255],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributes(), [
            'user_id' => 'User ID',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'headimgurl' => 'Headimgurl',
            'sex' => 'Sex',
            'birth_year' => 'Birth Year',
            'birth_month' => 'Birth Month',
            'birth_day' => 'Birth Day',
            'constellation' => 'Constellation',
            'zodiac' => 'Zodiac',
            'company' => 'Company',
            'education' => 'Education',
            'occupation' => 'Occupation',
            'position' => 'Position',
            'affective_status' => 'Affective Status',
            'lookingfor' => 'Lookingfor',
            'blood_type' => 'Blood Type',
            'height' => 'Height',
            'weight' => 'Weight',
            'interest' => 'Interest',
            'from' => '怎么来的',
            'want' => '想要什么',
            'recommand' => '推荐一个人',
        ]);
    }

}

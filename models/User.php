<?php
namespace someet\common\models;

use dektrium\user\models\Account;
use yii\behaviors\TimestampBehavior;

use someet\common\models\queries\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use dektrium\user\models\User as BaseUser;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $is_email_verified
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email_confirmation_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $password write-only password
 * @property string $wechat_id
 * @property integer $last_login_at
 */
class User extends BaseUser
{
    /** * 用户状态是删除 */
    const STATUS_DELETED = 0;
    /** * 用户状态是正常 */
    const STATUS_ACTIVE = 10;

    /** * 用户在白名单 */
    const WHITE_LIST_YES = 1;
    /** * 用户不在白名单 */
    const WHITE_LIST_NO = 0;

    /** * 用户在黑名单 */
    const BLACK_LIST_YES = 1;
    /** * 用户不在黑名单 */
    const BLACK_LIST_NO = 0;

    /**
     * @var string|null the current password value from form input
     */
    protected $_password;
    public $password_reset_token;
    public $email_confirmation_token;

    /**
     * @return UserQuery custom query class with user scopes
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'register' => ['username', 'password', 'unionid'],
            'update'   => ['id', 'mobile', 'wechat_id', 'last_login_at'],
            'default'   => ['mobile', 'wechat_id', 'last_login_at'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            //'usernameMatch'    => ['username', 'match', 'pattern' => static::$usernameRegexp],
            //'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
            //'usernameUnique'   => ['username', 'unique', 'message' => Yii::t('user', 'This username has already been taken')],
            //'usernameTrim'     => ['username', 'trim'],

            // email rules
            'emailPattern'  => ['email', 'email'],
            'emailLength'   => ['email', 'string', 'max' => 200],
            'emailUnique'   => ['email', 'unique', 'message' => Yii::t('user', 'This email address has already been taken')],
            'emailTrim'     => ['email', 'trim'],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength'   => ['password', 'string', 'min' => 6, 'on' => ['register', 'create']],

            [['email','password'], 'required', 'on'=>'signup'],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

            ['mobile', 'unique'],
            [['wechat_id'], 'unique'],
            [['last_login_at'], 'integer'],
            ['password_reset_token', 'string', 'max' => 60],
            ['email_confirmation_token', 'string', 'max' => 60],
            [['last_login_at', 'password_reset_token', 'email_confirmation_token'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->generateAuthKey();
            $this->generateEmailConfirmationToken();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            try {
                $params = Yii::$app->params;
                Yii::$app->mail->compose('confirmEmail', ['user' => $this])
                    ->setFrom([$params['support.email'] => $params['support.name']])
                    ->setTo($this->email)
                    ->setSubject('Complete registration with ' . Yii::$app->name)
                    ->send();
            } catch(\Exception $e) {
                Yii::warning('Failed to send confirmation email to new user. No SMTP server configured?','someet\common\models\User');
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        if (!empty($password)) {
            $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        }
    }

    /**
     * @return string|null the current password value, if set from form. Null otherwise.
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new email confirmation token
     * @param bool $save whether to save the record. Default is `false`.
     * @return bool|null whether the save was successful or null if $save was false.
     */
    public function generateEmailConfirmationToken($save = false)
    {
        $this->email_confirmation_token = Yii::$app->security->generateRandomString() . '_' . time();
        if ($save) {
            return $this->save();
        }
    }

    /**
     * Generates new password reset token
     * @param bool $save whether to save the record. Default is `false`.
     * @return bool|null whether the save was successful or null if $save was false.
     */
    public function generatePasswordResetToken($save = false)
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        if ($save) {
            return $this->save();
        }
    }

    /**
     * Resets to a new password and deletes the password reset token.
     * @param string $password the new password for this user.
     * @return bool whether the record was updated successfully
     */
    public function resetPassword($password)
    {
        $this->setPassword($password);
        $this->password_reset_token = null;
        return $this->save();
    }

    /**
     * Confirms an email an deletes the email confirmation token.
     * @return bool whether the record was updated successfully
     */
    public function confirmEmail()
    {
        $this->email_confirmation_token = null;
        $this->is_email_verified = 1;
        return $this->save();
    }

    // 活动列表
    public function getActivity()
    {
        return $this->hasMany(Activity::className(), ['created_by' => 'id']);
    }

    // 获得角色对象
    public function getAssignment()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }
}

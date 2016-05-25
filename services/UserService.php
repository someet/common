<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 5:55 PM
 */

namespace someet\common\services;

use someet\common\models\User;

class UserService extends User
{
    use ServiceError;

    /**
     * 根据unionid获取用户编号和access token
     *
     * @param string $unionid UNIONID
     * @return array
     */
    public function getUserinfoByUnionId($unionid)
    {
        //校验参数
        if (empty($unionid)) {
            $this->setError('uniondid不能为空');
            return false;
        }

        if (!is_string($unionid)) {
            $this->setError('unionid必须是字符串');
            return false;
        }

        if (strlen($unionid) > 60 || strlen($unionid) < 20) {
            $this->setError('传递的uniond位数不正确');
            return false;
        }

        $user = User::find()->where(['unionid' => $unionid])->one();
        if (!$user) {
            $this->setError('用户不存在');
            return false;
        }

        if ($user->access_token) {
            $data = [
                'user_id' => $user->id,
                'access_token' => $user->access_token
            ];
            return $data;
        }

        //生成access_token
        $time = time();
        $access_token = md5($user->id . md5($time . 'Someet'));

        $user->access_token = $access_token;
        if (!$user->save()) {
            $this->setError('更新用户Token失败');
            return false;
        }

        $data = [
            'user_id' => $user->id,
            'access_token' => $user->access_token
        ];
        return $data;
    }
}
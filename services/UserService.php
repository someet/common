<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 25/5/2016
 * Time: 5:55 PM
 */

namespace someet\common\services;

use someet\common\models\User;

class UserService extends BaseService
{

    /**
     * 根据unionid获取用户编号和access token
     *
     * @param string $unionid UNIONID
     * @return array
     */
    public function getUserinfoByUnionId($unionid)
    {
        $user = User::find()
            ->select(['id', 'username', 'created_at','updated_at','flags','allow_join_times','punish_score','mobile','status','wechat_id','last_login_at','last_active_at','join_count','attend_count','reject_count','activities_count','black_label','black_time'])
            ->where(['unionid' => $unionid])
            ->one();
        if (!$user) {
            $this->setError('用户不存在');
            return false;
        }

        if ($user->access_token) {
            return $user;
        }

        //生成access_token
        $time = time();
        $access_token = md5($user->id . md5($time . 'Someet'));

        $user->access_token = $access_token;
        if (!$user->save()) {
            $this->setError('更新用户Token失败');
            return false;
        }

        return $user;
    }
}
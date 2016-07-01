<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 14/6/2016
 * Time: 10:52 AM
 */

namespace someet\common\services;

use Yii;
use someet\common\models\AppDevice;

class AppDeviceService extends BaseService
{

    /**
     * 上报设备信息
     *
     * @param $data
     * @return bool
     */
    public function report($data)
    {
        $user_id = Yii::$app->user->id;
        $device_id = $data['device_id'];
        $platform = $data['platform'];
        $jiguang_id = $data['jiguang_id'];
        $alias_id = $data['alias_id'];
        $apple_token = $data['apple_token'];
        $app_name = $data['app_name'];
        $app_version = $data['app_version'];
        $device_model = $data['device_model'];
        $push_provider = $data['push_provider'];

        $appDevice = AppDevice::find()->where(['device_id' => $device_id])->one();
        if ($appDevice) {
            $appDevice->app_name = $app_name;
            $appDevice->app_version = $app_version;
            $appDevice->push_provider = $push_provider;
            if (!$appDevice->save()) {
                $errors = $appDevice->getFirstErrors();
                $this->setError(array_pop($errors));
                return false;
            }
            return true;
        }

        $appDevice = new AppDevice();
        $appDevice->user_id = $user_id;
        $appDevice->device_id = $device_id;
        $appDevice->platform = $platform;
        $appDevice->jiguang_id = $jiguang_id;
        $appDevice->alias_id = $alias_id;
        $appDevice->apple_token = $apple_token;
        $appDevice->app_name = $app_name;
        $appDevice->app_version = $app_version;
        $appDevice->device_model = $device_model;
        $appDevice->push_provider = $push_provider;
        if (!$appDevice->save()) {
            $errors = $appDevice->getFirstErrors();
            $this->setError(array_pop($errors));
            return false;
        }

        return true;
    }
}
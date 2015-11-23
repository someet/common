<?php

namespace someet\common\models;

use Yii;

/**
 * This is the model class for table "admin_log".
 *
 * @property integer $id
 * @property string $title
 * @property string $addtime
 * @property integer $admin_name
 * @property integer $admin_ip
 * @property integer $admin_agent
 * @property integer $controller
 * @property integer $action
 * @property integer $objId
 * @property integer $result
 */
class AdminLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_log}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'=>'操作记录ID',
            'title'=>'操作记录描述',
            'addtime'=>'记录时间',
            'admin_name'=>'操作人姓名',
            'admin_ip'=>'操作人IP地址',
            'admin_agent'=>'操作人浏览器代理商',
            'controller'=>'操作控制器名称',
            'action'=>'操作类型',
            'handle_id'=>'操作数据编号',
            'result'=>'操作结果',
        ];
    }

    /**
     * 保存日志
     * @param $result 例如登录成功等
     * @param $handle_id 如果操作了某个对象, 则将对象的id填充进来
     * @return null
     */
    public static function saveLog($result,$handle_id=null){
        $model = new self;
        //设置操作人的ip
        $model->admin_ip = Yii::$app->request->userIP;
        //设置操作时间
        $model->addtime = time();
        //如果能获得用户使用的浏览器代理则设置浏览器代理商名称
        $headers = Yii::$app->request->headers;
        if ($headers->has('User-Agent')) {
            $model->admin_agent =  $headers->get('User-Agent');
        }
        //设置操作人的用户id
        $model->admin_id = Yii::$app->user->identity->id;
        //设置操作人的用户名
        $model->admin_name = Yii::$app->user->identity->username;

        //控制器和方法过滤, 这里没过滤, 因为每次都要调整//
        /*
        $controllers = ['activity','activity-feed','activity-tag','activity-type','answer','qiniu','question','question-item','site', 'special', 'user'];
        if(!in_array(strtolower($controller),$controllers)) $controller = '';
        $actions = ['create','update','delete','login','logout'];
        if(!in_array(strtolower($action),$actions))$action = '';
        */
        //设置操作的控制器名称
        $model->controller = Yii::$app->controller->id;
        //设置操作的方法的名称
        $model->action = Yii::$app->controller->action->id;
        //设置操作后的结果
        $model->result = $result;
        //设置操作对象的id
        $model->handle_id = $handle_id;
        //设置标题显示什么
        $model->title =  $model->admin_name.' '.$model->action.' '.$model->controller;

        //尝试保存日志
        if(!$model->save(false)){
            //记录日志提示保存日志失败
            Yii::error('保存日志失败');
        }
    }
}

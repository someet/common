<?php

use yii\db\Schema;
use yii\db\Migration;

class m151030_072249_add_mobile_permission extends Migration
{
    public function up()
    {
        $items = [
            // 专题列表
            '/mobile/special/index' => ['user'],
            // 专题页面
            '/mobile/special/view' => ['user'],
            // 活动页面
            '/mobile/activity/view' => ['user'],
            // 首页 活动列表
            '/mobile/site/index' => ['user'],
            // 发送短信验证码
            '/mobile/sms/send-m-code' => ['user'],
            // 查看报名页面
            '/mobile/question/view-by-activity-id' => ['user'],
            // 完善用户信息页面
            '/mobile/member/finish' => ['user'],
            // 报名
            '/mobile/answer/create' => ['user'],
        ];

        $authItemTemplate = <<<SQL
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('%s', '2', '', null, null, null, null);
SQL;
        $itemChildTemplate = <<<SQL
        INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('%s', '%s');
SQL;
        $sql = '';
        foreach ($items as $item => $roles) {
            $sql .= sprintf($authItemTemplate, $item);
            foreach ($roles as $role) {
                $sql .= sprintf($itemChildTemplate, $role, $item);
            }
        }
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m151030_072249_add_mobile_permission cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

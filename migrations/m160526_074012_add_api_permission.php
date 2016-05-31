<?php

use yii\db\Schema;
use yii\db\Migration;

class m160526_074012_add_api_permission extends Migration
{
    public function up()
    {
        $items = [
            '/api/support-version/index' => ['user'],
            '/api/v1/user/index' => ['admin'],
            '/api/v1/auth-assignment/index' => ['admin'],
            '/api/v1/answer/filter' => ['admin', 'founder'],
            '/api/v1/activity/update-status' => ['pma'],
            '/api/v1/activity/punish-activity' => ['user'],
            '/api/v1/activity/appeal' => ['user'],
            '/api/v1/answer/create' => ['user'],
            '/api/v1/activity-feedback/create' => ['user'],
            '/api/v1/activity-feedback/view' => ['user'],
            '/api/v1/user/unionid' => ['user'],
            '/api/v1/activity/attend-activity' => ['user'],
            '/api/v1/activity/index' => ['user'],
            '/api/v1/user/view' => ['founder'],
            '/api/v1/question/view' => ['founder'],
            '/api/v1/answer/index' => ['founder'],
            '/api/v1/activity-check-in/check' => ['founder'],
            '/api/v1/answer/arrive' => ['founder'],
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
        echo "m160526_074012_add_api_permission cannot be reverted.\n";

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

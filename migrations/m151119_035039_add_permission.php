<?php

use yii\db\Schema;
use yii\db\Migration;

class m151119_035039_add_permission extends Migration
{
    public function up()
    {
        $items = [
            '/mobile/activity/index' => ['user'],
            '/mobile/site/index' => ['user'],
            '/mobile/member/finish-page' => ['user'],
            '/mobile/member/filled-answer' => ['user'],
            '/mobile/member/index' => ['user'],
            '/mobile/member/save-user-profile' => ['user'],
            '/mobile/answer/view-by-activity-id' => ['user'],
            '/mobile/activity-feedback/view' => ['user'],
            '/mobile/activity-feedback/create' => ['user'],
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
        echo "m151119_035039_add_permission cannot be reverted.\n";

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

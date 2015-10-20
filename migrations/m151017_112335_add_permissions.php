<?php

use yii\db\Schema;
use yii\db\Migration;

class m151017_112335_add_permissions extends Migration
{
    public function up()
    {
        $items = [
            '/backend/activity-type/index' => ['admin'],
            '/backend/activity-type/view' => ['admin'],
            '/backend/activity-type/create' => ['admin'],
            '/backend/activity-type/update' => ['admin'],
            '/backend/activity-type/delete' => ['admin'],
            '/backend/activity/index' => ['admin'],
            '/backend/activity/view' => ['admin'],
            '/backend/activity/create' => ['admin'],
            '/backend/activity/update' => ['admin'],
            '/backend/activity/delete' => ['admin'],
            '/backend/special/index' => ['admin'],
            '/backend/special/view' => ['admin'],
            '/backend/special/create' => ['admin'],
            '/backend/special/update' => ['admin'],
            '/backend/special/delete' => ['admin'],
            '/backend/user/index' => ['admin'],
            '/backend/user/view' => ['admin'],
            '/backend/user/create' => ['admin'],
            '/backend/user/update' => ['admin'],
            '/backend/user/delete' => ['admin'],
            '/backend/sms-template/index' => ['admin'],
            '/backend/sms-template/view' => ['admin'],
            '/backend/sms-template/create' => ['admin'],
            '/backend/sms-template/update' => ['admin'],
            '/backend/sms-template/delete' => ['admin'],
            '/backend/activity-feedback/index' => ['admin'],
            '/backend/activity-feedback/view' => ['admin'],
            '/backend/activity-feedback/create' => ['admin'],
            '/backend/activity-feedback/update' => ['admin'],
            '/backend/activity-feedback/delete' => ['admin'],
            '/backend/activity-tag/index' => ['admin'],
            '/backend/activity-tag/view' => ['admin'],
            '/backend/activity-tag/create' => ['admin'],
            '/backend/activity-tag/update' => ['admin'],
            '/backend/activity-tag/delete' => ['admin'],
            '/backend/answer/index' => ['admin'],
            '/backend/answer/view' => ['admin'],
            '/backend/answer/create' => ['admin'],
            '/backend/answer/update' => ['admin'],
            '/backend/answer/delete' => ['admin'],
            '/backend/question/index' => ['admin'],
            '/backend/question/view' => ['admin'],
            '/backend/question/create' => ['admin'],
            '/backend/question/update' => ['admin'],
            '/backend/question/delete' => ['admin'],
            '/backend/question-item/index' => ['admin'],
            '/backend/question-item/view' => ['admin'],
            '/backend/question-item/create' => ['admin'],
            '/backend/question-item/update' => ['admin'],
            '/backend/question-item/delete' => ['admin'],
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
        echo "m151017_112335_add_permissions cannot be reverted.\n";

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

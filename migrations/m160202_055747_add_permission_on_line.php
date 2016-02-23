<?php

use yii\db\Schema;
use yii\db\Migration;

class m160202_055747_add_permission_on_line extends Migration
{
    public function up()
    {
        $items = [
            /* 活动 */
            '/backend/activity/search' => ['pma'],
            '/backend/activity/list-by-type-id' => ['pma'],
            /* 活动标签 */
            '/backend/activity-tag/list' => ['pma'],
            /* 管理员日志相关 */
            '/backend/admin-log/index' => ['pma'],
            '/backend/admin-log/view' => ['pma'],
            /* 答案相关 */
            '/backend/answer/view-by-activity-id' => ['pma'],
            '/backend/answer/send-notification' => ['pma'],
            '/backend/answer/leave' => ['pma'],
            '/backend/answer/arrive' => ['pma'],
            /* 用户中心 */
            '/backend/member/index' => ['pma'],
            '/backend/member/update' => ['pma'],
            '/backend/member/search-by-auth' => ['pma'],
            '/backend/member/update-assignment' => ['pma'],
            '/backend/member/set-user-in-white-list' => ['pma'],
            /* 问题 */
            '/backend/question/view-by-activity-id' => ['pma'],
            /* 站点 */
            '/backend/site/fetch' => ['pma'],
            /* Uga答案 */
            '/backend/uga-answer/list' => ['pma'],
            '/backend/uga-answer/delete' => ['pma'],
            /* Uga问题 */
            '/backend/uga-question/create' => ['pma'],
            '/backend/uga-question/fetch' => ['pma'],
            '/backend/uga-question/list' => ['pma'],
            '/backend/uga-question/public' => ['pma'],
        ];

        $authItemTemplate = <<<SQL
INSERT INTO auth_item (name, type, description, rule_name, data, created_at, updated_at) VALUES ('%s', '2', '', null, null, null, null);
SQL;
        $itemChildTemplate = <<<SQL
        INSERT INTO auth_item_child (parent, child) VALUES ('%s', '%s');
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
        echo "m160202_055747_add_permission_on_line cannot be reverted.\n";

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

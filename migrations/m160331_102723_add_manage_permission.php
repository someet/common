<?php

use yii\db\Schema;
use yii\db\Migration;

class m160331_102723_add_manage_permission extends Migration
{
    public function up()
    {
        $items = [
            /* 黄牌系统 */
            '/backend/member/filter-prevent' => ['founder'],
            '/backend/member/update-status' => ['founder'],
            '/backend/member/update-all-prevent' => ['founder'],
            '/backend/member/apply' => ['founder'],
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
        echo "m160331_102723_add_manage_permission cannot be reverted.\n";

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

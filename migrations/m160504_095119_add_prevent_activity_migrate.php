<?php

use yii\db\Schema;
use yii\db\Migration;

class m160504_095119_add_prevent_activity_migrate extends Migration
{
    public function up()
    {
        $items = [
            '/backend/activity/filter-prevent' => ['founder'],
            '/backend/activity/update-status' => ['founder'],
            '/backend/activity/update-all-prevent' => ['founder'],
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
        echo "m160504_095119_add_prevent_activity_migrate cannot be reverted.\n";

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

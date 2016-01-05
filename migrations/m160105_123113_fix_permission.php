<?php

use yii\db\Schema;
use yii\db\Migration;

class m160105_123113_fix_permission extends Migration
{
    public function up()
    {
        //delete error permission
        $this->execute("DELETE FROM `auth_item_child` WHERE `parent`='founder' and`child`='mobile/answer/filter'");
        $this->execute("DELETE FROM `auth_item_child` WHERE `parent`='user' and`child`='mobile/member/complete-assist'");
        $this->execute("DELETE FROM `auth_item_child` WHERE `parent`='user' and`child`='mobile/member/complete-user'");
        $this->execute("DELETE FROM `auth_item_child` WHERE `parent`='user' and`child`='mobile/member/verify'");

        $this->execute("DELETE FROM `web`.`auth_item` WHERE `name`='mobile/answer/filter'");
        $this->execute("DELETE FROM `web`.`auth_item` WHERE `name`='mobile/member/complete-assist'");
        $this->execute("DELETE FROM `web`.`auth_item` WHERE `name`='mobile/member/complete-user'");
        $this->execute("DELETE FROM `web`.`auth_item` WHERE `name`='mobile/member/verify'");

        //readd user permission
        $items = [
            '/mobile/answer/filter' => ['founder'],
            '/mobile/member/complete-user' => ['user'],
            '/mobile/member/complete-assist' => ['user'],
            '/mobile/member/verify' => ['user'],
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

    }

    public function down()
    {
        echo "m160105_123113_fix_permission cannot be reverted.\n";

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

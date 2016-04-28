<?php

use yii\db\Schema;
use yii\db\Migration;

class m160422_082612_update_activity_by_role_permission extends Migration
{
    public function up()
    {

        $this->update('auth_item_child',['parent' => 'user'],'child = "/mobile/member/activity-by-role" ');

        return true;
    }

    public function down()
    {
        echo "m160422_082612_update_activity_by_role_permission cannot be reverted.\n";

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


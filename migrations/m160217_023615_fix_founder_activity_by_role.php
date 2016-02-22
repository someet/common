<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_023615_fix_founder_activity_by_role extends Migration
{
    public function up()
    {

        $this->update('auth_item_child',['parent' => 'founder'],'child = "/mobile/member/activity-by-role" ');

        return true;
    }

    public function down()
    {
        echo "m160217_023615_fix_founder_activity_by_role cannot be reverted.\n";

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

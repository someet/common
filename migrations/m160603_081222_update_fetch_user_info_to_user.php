<?php

use yii\db\Schema;
use yii\db\Migration;

class m160603_081222_update_fetch_user_info_to_user extends Migration
{
    public function up()
    {
        $sql = <<<SQL
UPDATE `auth_item_child` SET `parent`='user' WHERE `parent`='founder' and`child`='/api/v1/user/view';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160603_081222_update_fetch_user_info_to_user cannot be reverted.\n";

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

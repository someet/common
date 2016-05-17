<?php

use yii\db\Schema;
use yii\db\Migration;

class m160426_113053_update_noti_note_type_migrations extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `noti` CHANGE COLUMN `note` `note` text NOT NULL COMMENT '通知内容';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160426_113053_update_noti_note_type_migrations cannot be reverted.\n";

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

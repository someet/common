<?php

use yii\db\Schema;
use yii\db\Migration;

class m160613_054803_add_address_assign_permission extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE activity ADD COLUMN address_assign varchar(255) COMMENT '场地是否分配' AFTER address;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160613_054803_add_address_assign_permission cannot be reverted.\n";
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

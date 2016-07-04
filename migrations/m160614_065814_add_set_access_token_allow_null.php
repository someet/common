<?php

use yii\db\Schema;
use yii\db\Migration;

class m160614_065814_add_set_access_token_allow_null extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `user`
CHANGE COLUMN `access_token` `access_token` VARCHAR(32) NULL COMMENT '用于移动端访问的TOKEN' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160614_065814_add_set_access_token_allow_null cannot be reverted.\n";

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
